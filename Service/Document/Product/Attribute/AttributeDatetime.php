<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class AttributeDatetime
 *
 * Sync all product attributes of type "datetime_attributes"
 * (ex: the attributes declared with scope global or website and backend_type="datetime" in eav_attribute)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class AttributeDatetime extends AttributeAbstract
{

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "datetime_global";
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
        if($item[$attributeCode] == 0)
        {
            $item[$attributeCode] = NULL;
        }

        return $this->getDatetimeAttributeSchema([$item[$attributeCode]], $attributeName);
    }

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_DATETIME;
    }


}
