<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
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
    public const DI_SCHEMA_RESOLVER_TYPE = "eavAttributes";

    /**
     * Structure: [property-name => [$schema, $schema], property-name => [], [..]]
     *
     * @return array
     */
    public function getValues() : array
    {
        $content = [];
        $dataProvider = $this->getDataProvider();
        try {
            foreach($dataProvider->getData() as $row)
            {
                $schema = [];
                $attributeCode = $dataProvider->getCode($row);

                $schema[DocSchemaInterface::FIELD_NAME] = $this->sanitizePropertyName($attributeCode);
                $schema[DocSchemaInterface::FIELD_INTERNAL_ID] = $dataProvider->getInternalId($row);
                $schema[DocSchemaInterface::FIELD_LOCALIZED] =  $dataProvider->isLocalized($row);
                $schema[DocSchemaInterface::FIELD_MULTI_VALUE] =  $dataProvider->isMultivalue($row);
                $schema[DocSchemaInterface::FIELD_FILTER_BY] =  $dataProvider->isFilterBy($row);
                $schema[DocSchemaInterface::FIELD_FORMAT] =  $dataProvider->getFormat($row);
                $schema[DocSchemaInterface::FIELD_SEARCH_BY] = $dataProvider->getSearchBy($row);
                $schema[DocSchemaInterface::FIELD_SEARCH_SUGGESTION] = $dataProvider->isSearchSuggestion($row);
                $schema[DocSchemaInterface::FIELD_FILTER_BY] = $dataProvider->isFilterBy($row);
                $schema[DocSchemaInterface::FIELD_ORDER_BY] = $dataProvider->isOrderBy($row);
                $schema = $this->schemaGetter()->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_LABEL,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $dataProvider->getLabel($row)
                );
                $schema = $this->schemaGetter()->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_ATTRIBUTE_GROUP,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $dataProvider->getAttributeGroup($row)
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
