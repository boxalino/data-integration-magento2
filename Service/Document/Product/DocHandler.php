<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyInterface;
use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrateTrait;
use Boxalino\DataIntegration\Service\Document\DocMviewDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Status;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DocGeneratorInterface;
use Boxalino\DataIntegrationDoc\Generator\Product\Group;
use Boxalino\DataIntegrationDoc\Generator\Product\Line;
use Boxalino\DataIntegrationDoc\Generator\Product\Sku;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocProduct;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocProductHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\DeltaIntegrationInterface;
use Magento\Framework\DataObject;
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
    DocInstantIntegrationInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface,
    DocMviewDeltaIntegrationInterface
{

    use DocDeltaIntegrationTrait;
    use DocInstantIntegrationTrait;
    use DocMviewDeltaIntegrationTrait;
    use DiIntegrateTrait;

    public function __construct(
        LoggerInterface $logger,
        array $propertyHandlers = [],
        bool $instantMode = true
    ){
        parent::__construct($logger);
        $this->instantMode = $instantMode;

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
    protected function createDocLines() : void
    {
        $this->addSystemConfigurationOnHandlers();
        $this->generateDocData();

        $productGroups = $this->getDocProductGroups();
        $this->logTime("start" . __FUNCTION__);
        foreach($productGroups as $productGroup)
        {
            $this->addDocLine(
                $this->getDocSchemaGenerator()
                    ->setProductLine(
                        $this->getSchemaGeneratorByType(DocProductHandlerInterface::DOC_PRODUCT_LEVEL_LINE)
                            ->addProductGroup($productGroup)
                    )->setCreationTm(date("Y-m-d H:i:s"))
            );
        }
        unset($productGroups);

        $this->resetDocData();

        $this->logTime("end" . __FUNCTION__);
        $this->logMessage(__FUNCTION__, "end" . __FUNCTION__, "start" . __FUNCTION__);
    }

    /**
     * @return array
     */
    protected function getDocProductGroups() : array
    {
        $productGroups = [];
        $productSkus = [];

        $this->logInfo("Start to organize the DB load into product_groups & skus.");
        $this->logTime("start" . __FUNCTION__);
        foreach($this->getDocData() as $id => $content)
        {
            try{
                $id = (string)$content[DocSchemaInterface::FIELD_INTERNAL_ID];
                if(!isset($content[DocSchemaInterface::DI_DOC_TYPE_FIELD]))
                {
                    $this->logWarning("[doc_product] incomplete content for $id: "
                        . json_encode($content) . ". This error usually means the property handlers are misconfigured."
                    );

                    continue;
                }

                /** @var Group | Sku | Line | DocGeneratorInterface $schema */
                $schema = $this->getSchemaGeneratorByType($content[DocSchemaInterface::DI_DOC_TYPE_FIELD], $content);
                $parentIds = array_filter(explode(",", $content[DocSchemaInterface::DI_PARENT_ID_FIELD]));
                if(empty($parentIds))
                {
                    // the entity has no parent
                    $productGroups = $this->_treatProductGroupDocLine($id, $schema, $content, $productGroups);
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
                    // the entity is available as a parent to other entities
                    $productGroups = $this->_treatVariantSchema($id, DocProductHandlerInterface::DOC_PRODUCT_LEVEL_GROUP, $content, $productGroups);
                }

                // the entity is a child to other items & must exist replicated at the level of each entity as a SKU with the parent`s properties for status & visibility
                list($productGroups, $productSkus) = $this->_treatSkusWithParentIds($id, $schema, $content, $productGroups, $productSkus);
            } catch (\Throwable $exception)
            {
                if($this->getSystemConfiguration()->isTest())
                {
                    $this->logger->info($exception->getMessage());
                }
            }
        }

        $productGroups = $this->_loadSkusOnGroups($productSkus, $productGroups);
        unset($productSkus);

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
     * @return array
     */
    protected function _treatSkusWithParentIds(string $id, Sku $schema, array $content, array $productGroups, array $productSkus) : array
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
                    $productGroups = $this->_treatVariantSchema($id, DocProductHandlerInterface::DOC_PRODUCT_LEVEL_GROUP, $content, $productGroups);
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
                $status = $this->getChildStatusByParent($productGroups[$parentId]->getStatus(), $schema->getStatus());
                if(!is_null($status))
                {
                    $schema->setStatus($status);
                }
                $productGroups[$parentId]->addSkus([$schema]);

                continue;
            }

            if($this->getDiConfiguration()->getMode() === DeltaIntegrationInterface::INTEGRATION_MODE)
            {
                if(in_array($parentId, $this->getIds()))
                {
                    $productSkus[$parentId][] = $schema;
                }

                continue;
            }

            $productSkus[$parentId][] = $schema;
        }

        return [$productGroups, $productSkus];
    }

    /**
     * @param array $productSkus
     * @param array $productGroups
     * @return array
     */
    protected function _loadSkusOnGroups(array $productSkus, array $productGroups) : array
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
                    $status = $this->getChildStatusByParent($schema->getStatus(), $sku->getStatus());
                    if(is_null($status))
                    {
                        continue;
                    }

                    $sku->setStatus($status);
                } catch (\Throwable $exception)
                {
                }
            }

            $productGroups[$parentId] = $schema->addSkus($skus);
        }

        return $productGroups;
    }

    /**
     * Required for the use-case of a product being both a child and a parent
     * (ex: configurable products build up of other configurable products)
     *
     * @param string $id
     * @param string $docType
     * @param array $content
     * @param array $productGroups
     * @return array
     */
    protected function _treatVariantSchema(string $id, string $docType, array $content, array $productGroups) : array
    {
        $content = $this->_fixPropertyForDuplicateDoc($content, DocSchemaInterface::FIELD_VISIBILITY);
        $schema = $this->getSchemaGeneratorByType($docType, $content);

        return $this->_treatProductGroupDocLine($id, $schema, $content, $productGroups);
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
     * @return $array
     */
    protected function _treatProductGroupDocLine(string $id, Group $schema, array $content, array $productGroups) : array
    {
        $sku = $this->docTypePropDiffDuplicate(
            DocProductHandlerInterface::DOC_PRODUCT_LEVEL_GROUP,
            DocProductHandlerInterface::DOC_PRODUCT_LEVEL_SKU,
            $content
        );

        $schema->addSkus([$sku]);
        $productGroups[$id] = $schema;
        unset($sku); unset($schema);

        return $productGroups;
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

    /**
     * If the parent has status: disabled - children statuses must be updated to parent status
     *
     * @param array $parentStatus
     * @param array $childStatus
     * @return array | null
     */
    protected function getChildStatusByParent(array $parentStatus, array $childStatus) : ?array
    {
        $updated = false;
        /** @var Status (as array) $parent */
        foreach($parentStatus as $parent)
        {
            if(is_array($parent))
            {
                $parent = new DataObject($parent);
            }

            if($parent->getValue() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)
            {
                $updated = true;

                /** @var Status (as array) $child */
                foreach($childStatus as &$child)
                {
                    if(is_array($child))
                    {
                        $child = new DataObject($child);
                    }
                    if($child->getLanguage() === $parent->getLanguage())
                    {
                        $child->setValue($parent->getValue());
                    }

                    $child = $child->toArray();
                }
            }
        }

        if($updated)
        {
            return $childStatus;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function chunk() : bool
    {
        return false;
    }


}
