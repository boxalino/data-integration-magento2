<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Typed\StringLocalizedAttribute;

/**
 * Class AttributeVarcharLocalized
 *
 * Sync all product attributes of type "string_localized_attributes"
 * (ex: the attributes declared with scope store and backend_type="varchar" or "text" in eav_attribute)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class AttributeVarcharLocalized extends AttributeAbstract
{


    public function getSchema(array $item, array $languages, string $attributeName, string $attributeCode): DocPropertiesInterface
    {
        return $this->schemaGetter()->getRepeatedGenericLocalizedSchema($item, $languages, $attributeName, new StringLocalizedAttribute(), null);
    }

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_STRING_LOCALIZED;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "varchar_localized";
    }


}
