<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\AttributeValue;

use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeHelperTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Generator\DiPropertyTrait;
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

    use DiPropertyTrait;
    use AttributeHelperTrait;

    /**
     * Structure: [property-name => [$schema, $schema], property-name => [], [..]]
     *
     * @return array
     */
    public function getValues() : array
    {
        $content = [];
        try {
            foreach($this->getDataProvider()->getAttributes() as $attributeCode)
            {
                $this->getDataProvider()->setAttributeCode($attributeCode);
                $attributeName = $this->sanitizePropertyName($attributeCode);

                $content[$attributeName] = [];
                foreach($this->getDataProvider()->getData() as $id => $label)
                {
                    $schema = [];
                    $schema[DocSchemaInterface::FIELD_ATTRIBUTE_NAME] = $attributeName;
                    $schema[DocSchemaInterface::FIELD_NUMERICAL] = $this->getDataProvider()->isNumerical((string)$id);
                    $schema[DocSchemaInterface::FIELD_VALUE_ID] = (string)$id;
                    $this->addingLocalizedPropertyToSchema(
                        DocSchemaInterface::FIELD_VALUE_LABEL,
                        $schema,
                        $this->getSystemConfiguration()->getLanguages(),
                        $this->getDataProvider()->getValueLabel((string)$id)
                    );

                    $content[$attributeName][] = $schema;
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
