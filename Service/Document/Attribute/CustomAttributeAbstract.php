<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingSchemaDataProviderDefinitionException;

/**
 * Class CustomAttributeAbstract
 *
 * @package Boxalino\DataIntegration\Service\Document\Attribute
 */
abstract class CustomAttributeAbstract extends IntegrationPropertyHandlerAbstract
{
    public const DI_SCHEMA_RESOLVER_TYPE = "customAttribute";

    /**
     * Add here all custom attribute definitions
     * extend with other properties as defined in the doc_attribute schema
     * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280945/doc_attribute
     *
     * @return array
     */
    abstract function getCustomAttributesDefinition() : array;

    /**
     * Structure: [property-name => [$schema, $schema], property-name => [], [..]]
     * Extend with other configurations on the $schema as defined in the doc_attribute schema
     * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280945/doc_attribute
     *
     * @return array
     */
    public function getValues() : array
    {
        $content = [];
        try {
            /** @var \Boxalino\DataIntegration\Model\DataProvider\Document\Attribute\CustomAttributeAbstract $attribute */
            foreach($this->getCustomAttributesDefinition() as $attribute)
            {
                $schema = [];
                $attribute->setSystemConfiguration($this->getSystemConfiguration());
                $attributeCode = $attribute->getCode();

                $schema[DocSchemaInterface::FIELD_NAME] = $this->sanitizePropertyName($attributeCode);
                $schema[DocSchemaInterface::FIELD_INTERNAL_ID] = $attributeCode;
                $schema[DocSchemaInterface::FIELD_LOCALIZED] =  $attribute->isLocalized();
                $schema[DocSchemaInterface::FIELD_FILTER_BY] =  $attribute->isFilterBy();
                $schema[DocSchemaInterface::FIELD_MULTI_VALUE] =  $attribute->isMultivalue();
                $schema[DocSchemaInterface::FIELD_FORMAT] =  $attribute->getFormat();
                $schema[DocSchemaInterface::FIELD_SEARCH_SUGGESTION] =  $attribute->isSearchSuggestion();
                $schema[DocSchemaInterface::FIELD_ORDER_BY] =  $attribute->isOrderBy();
                $schema = $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_LABEL,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $attribute->getLabel()
                );

                $content[$attributeCode] = $schema;
            }
        } catch(MissingSchemaDataProviderDefinitionException $exception)
        {
            $this->logger->alert($exception->getMessage());
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType() : string
    {
        return self::DI_SCHEMA_RESOLVER_TYPE;
    }


}
