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
        try {
            foreach($this->getDataProvider()->getData() as $id)
            {
                $schema = [];
                $schema[DocSchemaInterface::FIELD_ATTRIBUTE_NAME] = $this->getDataProvider()->getAttributeName((string)$id);
                $schema[DocSchemaInterface::FIELD_NUMERICAL] = $this->getDataProvider()->isNumerical((string)$id);
                $schema[DocSchemaInterface::FIELD_VALUE_ID] = (string)$id;
                $schema[DocSchemaInterface::FIELD_PARENT_VALUE_IDS] = $this->getDataProvider()->getParentValueIds((string)$id);

                $status = $this->getDataProvider()->getStatus((string)$id);
                $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_STATUS,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $status
                );

                $name = $this->getDataProvider()->getValueLabel((string)$id);
                $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_VALUE_LABEL,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $name
                );

                $description = $this->getDataProvider()->getDescription((string)$id);
                $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_DESCRIPTION,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $description);

                $link = $this->getDataProvider()->getLink((string)$id);
                $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_LINK,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $link
                );

                $content[$this->getDataProvider()->getAttributeName((string)$id)][] = $schema;
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
