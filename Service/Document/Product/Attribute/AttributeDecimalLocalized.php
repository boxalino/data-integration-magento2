<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Typed\NumericLocalizedAttribute;

/**
 * Class AttributeDecimalLocalized
 *
 * Sync all product attributes of type "numeric_localized_attributes"
 * (ex: the attributes declared with scope store and backend_type="decimal"  in eav_attribute)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class AttributeDecimalLocalized extends AttributeAbstract
{

    public function getSchema(array $item, array $languages, string $attributeName, string $attributeCode): DocPropertiesInterface
    {
        return $this->getRepeatedGenericLocalizedSchema($item, $languages, $attributeName, new NumericLocalizedAttribute(), null);
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
        return "decimal_localized";
    }


}
