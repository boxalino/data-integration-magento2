<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\AttributeValue;

use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeHelperTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingSchemaDataProviderDefinitionException;

/**
 * Class EavAttributeSourceModel
 *
 * Exports the data about the product EAV attributes that have a source model defined
 *
 * This contains the model required for the content access
 * A valid resource is used as a data provider
 *
 * @package Boxalino\DataIntegration\Service\Document\AttributeValue
 */
class EavAttributeSourceModel extends IntegrationPropertyHandlerAbstract
{

    use AttributeHelperTrait;

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
            foreach($dataProvider->getAttributes() as $attributeCode)
            {
                $dataProvider->setAttributeCode($attributeCode);
                $attributeName = $this->sanitizePropertyName($attributeCode);

                $content[$attributeName] = [];
                foreach($dataProvider->getData() as $id => $label)
                {
                    $schema = [];
                    $schema[DocSchemaInterface::FIELD_ATTRIBUTE_NAME] = $attributeName;
                    $schema[DocSchemaInterface::FIELD_NUMERICAL] = $dataProvider->isNumerical((string)$id);
                    $schema[DocSchemaInterface::FIELD_VALUE_ID] = (string)$id;
                    $schema = $this->schemaGetter()->addingLocalizedPropertyToSchema(
                        DocSchemaInterface::FIELD_VALUE_LABEL,
                        $schema,
                        $this->getSystemConfiguration()->getLanguages(),
                        $dataProvider->getValueLabel((string)$id)
                    );

                    $content[$attributeName][] = $schema;
                    unset($schema);
                }
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
        return "eavAttributesSourceModel";
    }


}
