<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class AttributeVisibilityGrouping
 *
 * The attribute_visibility_grouping property (array) is set at the level of GROUP
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252149870/doc%2Bproduct#Properties-specific-to-the-product-group-%26-SKU
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class AttributeVisibilityGrouping extends IntegrationPropertyHandlerAbstract
{

    /**
     * @return array
     */
    public function _getValues() : array
    {
        $content = [];

        /** @var array $item columns: di_id, <attribute_code> as value ..  */
        foreach($this->getDataProvider()->getData() as $item)
        {
            $id = $this->_getDocKey($item);
            if(!isset($content[$id]))
            {
                $content[$id][$this->getResolverType()] = [];
            }

            try{
                $content[$id][$this->getResolverType()] = $this->getSchema($item);
            } catch (\Throwable $exception)
            {
                if($this->logErrors())
                {
                    $this->logger->warning("Error on ". $this->getResolverType() . "with exception: "
                        . $exception->getMessage() . " on " . json_encode($item)
                    );
                }
            }
        }

        if($this->logErrors())
        {
            $this->logger->info(count($content) . " items have content for " . $this->getResolverType()
                . ". Configured attribute " . $this->getAttributeCode());
        }

        return $content;
    }

    /**
     * Per document data-structure, the attribute has as values a list
     *
     * @param array $item
     * @return array
     */
    public function getSchema(array $item) : array
    {
        return array_filter(explode(",", $item[$this->getAttributeCode()]),'strlen');
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_ATTRIBUTE_VISIBILITY_GROUPING;
    }


}
