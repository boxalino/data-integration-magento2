<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

/**
 * Class AttributeConfigurationOnDataProviderTrait
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
trait AttributeConfigurationOnDataProviderTrait
{

    /**
     * @var array
     */
    protected $attribute;

    /**
     * Prepare the dataprovider for the new context
     */
    public function _addAttributeConfigOnDataProviderByAttribute() : void
    {
        if(isset($this->attribute["attribute_code"]))
        {
            $this->getDataProvider()->setAttributeCode($this->attribute['attribute_code']);
        }

        if(isset($this->attribute["attribute_id"]))
        {
            $this->getDataProvider()->setAttributeId((int)$this->attribute['attribute_id']);
        }
    }

    /**
     * @return array
     */
    public function _getPropertyNameAndAttributeCode() : array
    {
        $attributeCode = $this->getAttributeCode();
        if(isset($this->attribute['attribute_code']))
        {
            $attributeCode = $this->attribute['attribute_code'];
        }

        return [$attributeCode, $this->sanitizePropertyName($attributeCode)];
    }

    /**
     * @return array
     */
    public function getAttribute(): array
    {
        return $this->attribute;
    }

    /**
     * @param array $attribute
     * @return void
     */
    public function setAttribute(array $attribute): void
    {
        $this->attribute = $attribute;
    }


}
