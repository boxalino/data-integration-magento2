<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Typed\DatetimeLocalizedAttribute;

/**
 * Class AttributeDatetimeLocalized
 *
 * Sync all product attributes of type "datetime_localized_attributes"
 * (ex: the attributes declared with scope store and backend_type="datetime" in eav_attribute)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class AttributeDatetimeLocalized extends AttributeAbstract
{

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "datetime_localized";
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
        return $this->schemaGetter()->getRepeatedGenericLocalizedSchema($item, $languages, $attributeName, new DatetimeLocalizedAttribute(), null);
    }

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_DATETIME_LOCALIZED;
    }


}
