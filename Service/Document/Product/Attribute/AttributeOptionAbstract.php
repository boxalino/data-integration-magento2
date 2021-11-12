<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class AttributeOptionAbstract
 *
 * Sync product attributes which are identified to have option labels/values
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
abstract class AttributeOptionAbstract extends AttributeAbstract
{

    public function getSchema(array $item, array $languages, string $attributeName, string $attributeCode): DocPropertiesInterface
    {
        return $this->getRepeatedLocalizedSchema($item, $languages, $attributeName, $attributeCode);
    }

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_STRING_LOCALIZED;
    }
    

}
