<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class AttributeDecimal
 *
 * Sync all product attributes of type "numeric_attributes"
 * (ex: the attributes declared with scope global or website and backend_type="decimal" in eav_attribute)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class AttributeDecimal extends AttributeAbstract
{

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_NUMERIC;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "decimal_global";
    }

    /**
     * @param array $item
     * @param array $languages
     * @param string $attributeName
     * @param string $attributeCode
     * @return DocPropertiesInterface
     */
    public function getSchema(array $item, array $languages, string $attributeName, string $attributeCode): DocPropertiesInterface
    {
        return $this->schemaGetter()->getNumericAttributeSchema([$item[$attributeCode]], $attributeName, null);
    }


}
