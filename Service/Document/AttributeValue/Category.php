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
            foreach($dataProvider->getData() as $row)
            {
                $schema = [];
                $schema[DocSchemaInterface::FIELD_ATTRIBUTE_NAME] = $dataProvider->getAttributeName((string)$row["entity_id"]);
                $schema[DocSchemaInterface::FIELD_NUMERICAL] = $dataProvider->isNumerical((string)$row["entity_id"]);
                $schema[DocSchemaInterface::FIELD_VALUE_ID] = (string)$row["entity_id"];
                $schema[DocSchemaInterface::FIELD_PARENT_VALUE_IDS] = $dataProvider->getParentValueIds((string)$row["entity_id"]);

                $status = $dataProvider->getStatus((string)$row["entity_id"]);
                $schema = $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_STATUS,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $status
                );

                $name = $dataProvider->getValueLabel((string)$row["entity_id"]);
                $schema = $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_VALUE_LABEL,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $name
                );

                $description = $dataProvider->getDescription((string)$row["entity_id"]);
                $schema = $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_DESCRIPTION,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $description);

                $link = $dataProvider->getLink((string)$row["entity_id"]);
                $schema = $this->addingLocalizedPropertyToSchema(
                    DocSchemaInterface::FIELD_LINK,
                    $schema,
                    $this->getSystemConfiguration()->getLanguages(),
                    $link
                );

                $schema[DocSchemaInterface::FIELD_STRING][] =
                    $this->getStringAttributeSchema([$row["path"]], "path")->toArray();

                $schema[DocSchemaInterface::FIELD_NUMERIC][] =
                    $this->getNumericAttributeSchema([$row["position"]], "position")->toArray();

                $schema[DocSchemaInterface::FIELD_NUMERIC][] =
                    $this->getNumericAttributeSchema([$row["level"]], "level")->toArray();

                $schema[DocSchemaInterface::FIELD_NUMERIC][] =
                    $this->getNumericAttributeSchema([$row["parent_id"]], "parent_id")->toArray();

                $content[$dataProvider->getAttributeName((string)$row["entity_id"])][] = $schema;
                unset($schema);
            }
        } catch(MissingSchemaDataProviderDefinitionException $exception)
        {
            $this->logger->alert($exception->getMessage());
        } catch (\Throwable $exception)
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
