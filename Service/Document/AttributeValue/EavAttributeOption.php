<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\AttributeValue;

use Boxalino\DataIntegration\Api\DataProvider\DocAttributeValueLineInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingSchemaDataProviderDefinitionException;

/**
 * Class EavAttributeOption
 *
 * Exports the data about the product EAV attributes options
 * This contains the model required for the content access
 * A valid resource is used as a data provider
 *
 * @package Boxalino\DataIntegration\Service\Document\AttributeValue
 */
class EavAttributeOption extends IntegrationPropertyHandlerAbstract
{

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
            foreach($dataProvider->getData() as $id => $attributeCode)
            {
                $dataProvider->setAttributeCode($attributeCode);
                $attributeName = $this->sanitizePropertyName($attributeCode);
                if(!isset($content[$attributeName]))
                {
                    $content[$attributeName] = [];
                }

                $schema = [];
                $schema[DocSchemaInterface::FIELD_ATTRIBUTE_NAME] = $attributeName;
                $schema[DocSchemaInterface::FIELD_NUMERICAL] = $dataProvider->isNumerical((string)$id);
                $schema[DocSchemaInterface::FIELD_VALUE_ID] = (string)$id;

                $name = $dataProvider->getValueLabel((string)$id);
                $schema = $this->schemaGetter()->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_VALUE_LABEL,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $name
                );

                /** adding the admin storeview value as string property key */
                $key = $dataProvider->getKey((string)$id);
                if(!is_null($key))
                {
                    $schema[DocSchemaInterface::FIELD_STRING][] =
                        $this->schemaGetter()->getStringAttributeSchema([$key], DocAttributeValueLineInterface::STRING_ATTRIBUTES_KEY)->toArray();
                }

                /** adding the swatch value as string property swatch */
                $swatch = $dataProvider->getSwatch((string)$id);
                if(!is_null($swatch))
                {
                    $schema[DocSchemaInterface::FIELD_STRING][] =
                        $this->schemaGetter()->getStringAttributeSchema([$swatch], DocAttributeValueLineInterface::STRING_ATTRIBUTES_SWATCH)->toArray();
                }

                /** adding the sort_order value as string property sort_order */
                $sortOrder = $dataProvider->getSortOrder((string)$id);
                if(!is_null($sortOrder))
                {
                    $schema[DocSchemaInterface::FIELD_STRING][] =
                        $this->schemaGetter()->getStringAttributeSchema([$sortOrder], DocAttributeValueLineInterface::STRING_ATTRIBUTES_SORT_ORDER)->toArray();
                }

                $content[$attributeName][] = $schema;
                unset($schema);
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
        return "eavAttributesOption";
    }


}
