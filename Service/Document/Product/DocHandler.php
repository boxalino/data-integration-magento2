<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyInterface;
use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrateTrait;
use Boxalino\DataIntegration\Service\Document\DocMviewDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DocGeneratorInterface;
use Boxalino\DataIntegrationDoc\Generator\Product\Group;
use Boxalino\DataIntegrationDoc\Generator\Product\Line;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Generator\Product\Sku;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocProduct;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocProductHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Psr\Log\LoggerInterface;

/**
 * Class DocHandler
 *
 * 1. Handles the attributes definitions (what property under what schema is exported) : addSchemaDefinition
 * 2. Declares attribute handlers (what property and how is exported)
 * 3. Declares the export logic (product_line / product_groups / skus level elements)
 * 4. Creates the doc_product https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252149870/doc+product
 *
 * Aspects to consider:
 * 1. every sales channel has 1 Boxalino index
 * 2. every sales channel has 1 root navigation category ID
 *  - based on the navigation category ID it can be identified to which channel/data index the product ID belongs to
 * 3. all Boxalino accounts are updated with the products from the channel linked to the account
 *
 * @package Boxalino\DataIntegration\Service\Document\Product
 */
class DocHandler extends DocProduct implements
    DocProductHandlerInterface,
    DocDeltaIntegrationInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface,
    DocMviewDeltaIntegrationInterface
{

    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocMviewDeltaIntegrationTrait;
    use DiIntegrateTrait;

    public function __construct(
        LoggerInterface $logger,
        array $propertyHandlers = []
    ){
        parent::__construct($logger);
        foreach($propertyHandlers as $key => $propertyHandler)
        {
            if($propertyHandler instanceof DocSchemaPropertyHandlerInterface)
            {
                $this->addPropertyHandler($propertyHandler);
            }
        }
    }

    /**
     * The products are exported at the level of the product_groups
     * Children (skus) are being generated if:
     * 1. there are variants
     * 2. there are no variants
     *
     * For instant update use - the schema will be reduced to the properties that require
     * to be updated instantly
     */
    protected function createDocLines() : self
    {
        $this->addSystemConfigurationOnHandlers();
        $this->generateDocData();

        $productGroups = $this->getDocProductGroups();

        $this->logTime("start" . __FUNCTION__);
        foreach($productGroups as $productGroup)
        {
            $document = $this->getDocSchemaGenerator();

            /** @var Line $productLine */
            $productLine = $this->getSchemaGeneratorByType(DocProductHandlerInterface::DOC_PRODUCT_LEVEL_LINE);
            $productLine->addProductGroup($productGroup);

            $document->setProductLine($productLine)->setCreationTm(date("Y-m-d H:i:s"));
            $this->addDocLine($document);
        }

        $this->logTime("end" . __FUNCTION__);
        $this->logMessage(__FUNCTION__, "end" . __FUNCTION__, "start" . __FUNCTION__);

        return $this;
    }

    /**
     * @return array
     */
    protected function getDocProductGroups() : array
    {
        $productGroups = [];
        $productSkus = [];

        $this->logTime("start" . __FUNCTION__);
        foreach($this->getDocData() as $id => $content)
        {
            $id = (string)$content[DocSchemaInterface::FIELD_INTERNAL_ID];
            try{
                if(!isset($content[DocSchemaInterface::DI_DOC_TYPE_FIELD]))
                {
                    if($this->getSystemConfiguration()->isTest())
                    {
                        $this->getLogger()->warning("Boxalino DI: incomplete content for $id: "
                            . json_encode($content) . ". This error usually means the property handlers are misconfigured."
                        );
                    }

                    continue;
                }

                /** @var Group | Sku | Line | DocGeneratorInterface $schema */
                $schema = $this->getSchemaGeneratorByType($content[DocSchemaInterface::DI_DOC_TYPE_FIELD], $content);
                $parentIds = array_filter(explode(",", $content[DocSchemaInterface::DI_PARENT_ID_FIELD]));
                if(empty($parentIds))
                {
                    $this->_treatProductGroupDocLine($id, $schema, $content, $productGroups);
                }

                if(empty($parentIds) && empty($content[DocSchemaInterface::DI_AS_VARIANT]))
                {
                    continue;
                }

                if($content[DocSchemaInterface::DI_AS_VARIANT] === DocProductHandlerInterface::DOC_PRODUCT_LEVEL_GROUP)
                {
                    if(empty($parentIds))
                    {
                        continue;
                    }
                    $this->_treatVariantSchema($id, $content[DocSchemaInterface::DI_AS_VARIANT], $content, $productGroups);
                }

                $this->_treatSkusWithParentIds($id, $schema, $content, $productGroups, $productSkus);
            } catch (\Throwable $exception)
            {
                $this->logger->info($exception->getMessage());
            }
        }

        $this->_loadSkusOnGroups($productSkus, $productGroups);

        $this->logTime("end" . __FUNCTION__);
        $this->logMessage(__FUNCTION__, "end" . __FUNCTION__, "start" . __FUNCTION__);

        return $productGroups;
    }

    /**
     * A product that has multiple parent IDs must exist as a sku on it`s own
     * and as a reference (duplicate) at the level of the parents
     *
     * @param string $id
     * @param Sku $schema
     * @param array $content
     * @param array $productGroups
     * @param array $productSkus
     */
    protected function _treatSkusWithParentIds(string $id, Sku $schema, array $content, array &$productGroups, array &$productSkus) : void
    {
        $duplicate = count(array_filter(explode(",", $content[DocSchemaInterface::DI_PARENT_ID_FIELD]))) > 1;
        $parentIdTypesList = array_combine(
            array_filter(explode(",", $content[DocSchemaInterface::DI_PARENT_ID_FIELD])),
            array_filter(explode(",", $content[DocSchemaInterface::DI_PARENT_ID_TYPE_FIELD]))
        );

        foreach($parentIdTypesList as $parentId => $parentType)
        {
            if($duplicate)
            {
                if(!isset($productGroups[$id]))
                {
                    $this->_treatVariantSchema($id, DocProductHandlerInterface::DOC_PRODUCT_LEVEL_GROUP, $content, $productGroups);
                }

                /** @var Sku DocGeneratorInterface $schema */
                $schema = $this->getSchemaGeneratorByType($content[DocSchemaInterface::DI_DOC_TYPE_FIELD], $content);
                $schema->setInternalId($parentId . "_" . $id)
                    ->setExternalId($id)
                    ->setIndividuallyVisible(false);
            }

            if(isset($productGroups[$parentId]))
            {
                /** @var Group $parent */
                $schema->setVisibility($productGroups[$parentId]->getVisibility());
                $productGroups[$parentId]->addSkus([$schema]);

                continue;
            }

            $productSkus[$parentId][] = $schema;
        }
    }

    /**
     * @param array $productSkus
     * @param array $productGroups
     */
    protected function _loadSkusOnGroups(array &$productSkus, array &$productGroups) : void
    {
        foreach($productSkus as $parentId => $skus)
        {
            /** @var Group $schema by default - on product update event - the main variant is also exported*/
            $schema = $this->getSchemaGeneratorByType(
                DocProductHandlerInterface::DOC_PRODUCT_LEVEL_GROUP,
                [DocSchemaInterface::FIELD_INTERNAL_ID => (string)$parentId]
            );
            if(isset($productGroups[$parentId]))
            {
                $schema = $productGroups[$parentId];
            }

            foreach($skus as &$sku)
            {
                try{
                    /** @var Sku $sku */
                    $sku->setVisibility($schema->getVisibility());
                } catch (\Throwable $exception)
                {
                }
            }

            $productGroups[$parentId] = $schema->addSkus($skus);
        }
    }

    /**
     * Required for the use-case of a product being both a child and a parent
     * (ex: configurable products build up of other configurable products)
     *
     * @param string $id
     * @param string $docType
     * @param array $content
     * @param array $productGroups
     */
    protected function _treatVariantSchema(string $id, string $docType, array $content, array &$productGroups) : void
    {
        $content = $this->_fixPropertyForDuplicateDoc($content, DocSchemaInterface::FIELD_VISIBILITY);
        $schema = $this->getSchemaGeneratorByType($docType, $content);

        $this->_treatProductGroupDocLine($id, $schema, $content, $productGroups);
    }

    /**
     * Every product without a parent ID must exist both at the level of product_groups and skus
     * - creates the product as an SKU
     * - adds it as an SKU to the GROUP schema
     *
     * @param string $id
     * @param Group $schema
     * @param array $content
     * @param array $productGroups
     */
    protected function _treatProductGroupDocLine(string $id, Group $schema, array $content, array &$productGroups) : void
    {
        $sku = $this->docTypePropDiffDuplicate(
            DocProductHandlerInterface::DOC_PRODUCT_LEVEL_GROUP,
            DocProductHandlerInterface::DOC_PRODUCT_LEVEL_SKU,
            $content
        );

        $schema->addSkus([$sku]);
        $productGroups[$id] = $schema;
    }

    /**
     * @param array $content
     * @param string $propertyName
     * @return void
     */
    protected function _fixPropertyForDuplicateDoc(array $content, string $propertyName) : array
    {
        $replacePropertyName = DocProductPropertyInterface::DOC_SCHEMA_CONTEXTUAL_PROPERTY_PREFIX . $propertyName;
        if(isset($content[$replacePropertyName]))
        {
            $content[$propertyName] = $content[$replacePropertyName];
        }

        return $content;
    }


}
