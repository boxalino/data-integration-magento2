<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;

/**
 * Class AttributeAbstract
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
abstract class AttributeAbstract extends IntegrationPropertyHandlerAbstract
{

    /**
     * The flow for accessing/loading different <type>_attributes properties is same
     *
     * @return array
     */
    function getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();

        foreach($this->getDataProvider()->getData() as $attribute)
        {
            $this->_addAttributeConfigOnDataProviderByAttribute($attribute);
            list($attributeCode, $attributeName) = $this->_getPropertyNameAndAttributeCode($attribute);

            /** @var array $item columns di_id, <attributeCode> with value */
            foreach($this->getDataProvider()->getDataForAttribute() as $id => $item)
            {
                if($item instanceof \ArrayIterator)
                {
                    $item = $item->getArrayCopy();
                }

                if(!isset($content[$item[$this->getDiIdField()]][$this->getDocSchemaPropertyNode()]))
                {
                    $content[$item[$this->getDiIdField()]][$this->getDocSchemaPropertyNode()] = [];
                }

                $content[$item[$this->getDiIdField()]][$this->getDocSchemaPropertyNode()][] =
                    $this->getSchema($item, $languages, $attributeName, $attributeCode);

            }
        }

        return $content;
    }

    /**
     * @param array $item
     * @param array $languages
     * @param string $attributeName
     * @param string $attributeCode
     * @return DocPropertiesInterface
     */
    abstract function getSchema(array $item, array $languages, string $attributeName, string $attributeCode) : DocPropertiesInterface;


    /**
     * @param array $attribute
     */
    protected function _addAttributeConfigOnDataProviderByAttribute(array $attribute) : void
    {
        if(isset($attribute["attribute_code"]))
        {
            $this->dataProvider->setAttributeCode($attribute['attribute_code']);
        }

        if(isset($attribute["attribute_id"]))
        {
            $this->dataProvider->setAttributeId((int)$attribute['attribute_id']);
        }
    }

    /**
     * @param array $attribute
     * @return array
     */
    protected function _getPropertyNameAndAttributeCode(array $attribute) : array
    {
        $attributeCode = $this->getAttributeCode();
        if(isset($attribute['attribute_code']))
        {
            $attributeCode = $attribute['attribute_code'];
        }

        return [$attributeCode, $this->sanitizePropertyName($attributeCode)];
    }


}
