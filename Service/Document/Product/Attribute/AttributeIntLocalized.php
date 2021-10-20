<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Typed\NumericLocalizedAttribute;

/**
 * Class AttributeIntLocalized
 *
 * Sync all product attributes of type "numeric_localized_attributes"
 * (ex: the attributes declared with scope store and backend_type="int"  in eav_attribute)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class AttributeIntLocalized extends AttributeAbstract
{

    public function getSchema(array $item, array $languages, string $attributeName, string $attributeCode): DocPropertiesInterface
    {
        return $this->getRepeatedGenericLocalizedSchema($item, $languages, $attributeName, new NumericLocalizedAttribute(), $this->getDiIdField());
    }

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_NUMERIC_LOCALIZED;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "int_localized";
    }


}
