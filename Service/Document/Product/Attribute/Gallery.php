<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class Gallery
 *
 * Sync all product attributes of type "string_attributes"
 * (ex: the attributes declared with backend_type="media" in eav_attribute)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Gallery extends AttributeAbstract
{

    /**
     * @param array $item
     * @param array $languages
     * @param string $attributeName
     * @param string $attributeCode
     * @return DocPropertiesInterface
     */
    public function getSchema(array $item, array $languages, string $attributeName, string $attributeCode): DocPropertiesInterface
    {
        $values = array_filter(explode(",", $item[$attributeCode]),'strlen');
        return $this->getStringAttributeSchema($values, $attributeName);
    }

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_STRING;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "gallery";
    }


}
