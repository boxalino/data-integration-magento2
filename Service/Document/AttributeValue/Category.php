<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\AttributeValue;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingSchemaDataProviderDefinitionException;

/**
 * Class Category
 *
 * Exports the data about the categories
 * This contains the model required for the content access
 * A valid resource is used as a data provider
 *
 * @package Boxalino\DataIntegration\Service\Document\AttributeValue
 */
class Category extends IntegrationPropertyHandlerAbstract
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
            foreach($dataProvider->getData() as $id)
            {
                $schema = [];
                $schema[DocSchemaInterface::FIELD_ATTRIBUTE_NAME] = $dataProvider->getAttributeName((string)$id);
                $schema[DocSchemaInterface::FIELD_NUMERICAL] = $dataProvider->isNumerical((string)$id);
                $schema[DocSchemaInterface::FIELD_VALUE_ID] = (string)$id;
                $schema[DocSchemaInterface::FIELD_PARENT_VALUE_IDS] = $dataProvider->getParentValueIds((string)$id);

                $status = $dataProvider->getStatus((string)$id);
                $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_STATUS,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $status
                );

                $name = $dataProvider->getValueLabel((string)$id);
                $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_VALUE_LABEL,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $name
                );

                $description = $dataProvider->getDescription((string)$id);
                $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_DESCRIPTION,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $description);

                $link = $dataProvider->getLink((string)$id);
                $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_LINK,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $link
                );

                $content[$dataProvider->getAttributeName((string)$id)][] = $schema;
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
        return DocSchemaInterface::FIELD_CATEGORIES;
    }


}
