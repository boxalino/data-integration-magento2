<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Generator\DiPropertyTrait;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingSchemaDataProviderDefinitionException;

/**
 * Class EavAttribute
 * Exports the data about the categories
 * This contains the model required for the content access
 * A valid resource is used as a data provider
 *
 * @package Boxalino\DataIntegration\Service\Document\Attribute
 */
class EavAttribute extends IntegrationPropertyHandlerAbstract
{
    use DiPropertyTrait;

    public const DI_SCHEMA_RESOLVER_TYPE = "eavAttributes";

    /**
     * Structure: [property-name => [$schema, $schema], property-name => [], [..]]
     *
     * @return array
     */
    public function getValues() : array
    {
        $content = [];
        $content[DocSchemaInterface::FIELD_CATEGORIES] = [];
        try {
            foreach($this->getDataProvider()->getData() as $row)
            {
                $schema = [];
                $attributeCode = $this->getDataProvider()->getCode($row);

                $schema[DocSchemaInterface::FIELD_NAME] = $this->sanitizePropertyName($attributeCode);
                $schema[DocSchemaInterface::FIELD_INTERNAL_ID] = $this->getDataProvider()->getInternalId($row);
                $schema[DocSchemaInterface::FIELD_LOCALIZED] =  $this->getDataProvider()->isLocalized($row);
                $schema[DocSchemaInterface::FIELD_MULTI_VALUE] =  $this->getDataProvider()->isMultivalue($row);
                $schema[DocSchemaInterface::FIELD_FILTER_BY] =  $this->getDataProvider()->isFilterBy($row);
                $schema[DocSchemaInterface::FIELD_FORMAT] =  $this->getDataProvider()->getFormat($row);
                $schema[DocSchemaInterface::FIELD_SEARCH_BY] = $this->getDataProvider()->getSearchBy($row);
                $schema[DocSchemaInterface::FIELD_SEARCH_SUGGESTION] = $this->getDataProvider()->isSearchSuggestion($row);
                $schema[DocSchemaInterface::FIELD_FILTER_BY] = $this->getDataProvider()->isFilterBy($row);
                $schema[DocSchemaInterface::FIELD_ORDER_BY] = $this->getDataProvider()->isOrderBy($row);
                $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_LABEL,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $this->getDataProvider()->getLabel($row)
                );
                $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_ATTRIBUTE_GROUP,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $this->getDataProvider()->getAttributeGroup($row)
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
