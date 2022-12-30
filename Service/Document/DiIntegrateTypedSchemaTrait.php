<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegration\Api\DataProvider\DocSchemaTypedInterface;
use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingRequiredPropertyException;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\NoRecordsFoundException;

/**
 * Trait DiIntegrateTypedSchemaTrait
 * Asistant logic for getting fields on property handlers that are of type DocSchemaTypedInterface
 * 
 * @package Boxalino\DataIntegration\Service\Document
 */
trait DiIntegrateTypedSchemaTrait
{

    public function getValuesForTypedSchema() : array
    {
        $content = [];
        /** @var DocSchemaTypedInterface $dataProvider */
        $dataProvider = $this->getDataProvider();
        $propertyNames = $this->getSchemaPropertyNames();

        foreach($dataProvider->getData() as $item)
        {
            $id = $this->_getDocKey($item);
            if(!$id)
            {
                continue;
            }

            if(!isset($content[$id]))
            {
                $content[$id] = [];
            }

            try{
                $schema = $this->getPropertyHandlerSchema();
                foreach($dataProvider->getStringOptions($item) as $optionLabel => $optionValues)
                {
                    $schema->addStringAttribute(
                        $this->getStringAttributeSchema($optionValues, $optionLabel)
                    );
                }

                foreach($dataProvider->getNumericOptions($item) as $optionLabel => $optionValues)
                {
                    $schema->addNumericAttribute(
                        $this->getNumericAttributeSchema($optionValues, $optionLabel, null)
                    );
                }

                foreach($dataProvider->getDateTimeOptions($item) as $optionLabel => $optionValues)
                {
                    $schema->addDatetimeAttribute(
                        $this->getDatetimeAttributeSchema($optionValues, $optionLabel)
                    );
                }

                foreach($propertyNames as $propertyName)
                {
                    $propertyValue = $dataProvider->get($propertyName, $item);
                    if(is_null($propertyValue))
                    {
                        continue;
                    }
                    $schema->set($propertyName, $propertyValue);
                }

                $content[$id][$this->getResolverType()][] = $schema->toArray();
            } catch (\Throwable $exception)
            {
                $this->logDebug("Error on " . get_called_class() . " content export: " . $exception->getMessage());
            }
        }

        return $content;
    }

    public function getValuesForEntityTypedSchema() : array
    {
        $content = [];
        /** @var DocSchemaTypedInterface $dataProvider */
        $dataProvider = $this->getDataProvider();
        $propertyNames = $this->getSchemaPropertyNames();
        foreach($dataProvider->getData() as $item)
        {
            try{
                $id = $this->_getDocKey($item);
                $content[$id] = [];

                foreach($propertyNames as $propertyName)
                {
                    $propertyValue = $dataProvider->get($propertyName, $item);
                    if(is_null($propertyValue))
                    {
                        continue;
                    }

                    $content[$id][$propertyName] = $propertyValue;
                }
            } catch (MissingRequiredPropertyException $exception)
            {
                $this->logWarning("Missing required property : " . $exception->getMessage() . " on " . json_encode($item));

                unset($content[$id]);
                continue;
            }

            foreach($dataProvider->getStringOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_STRING][] = $this->getStringAttributeSchema($optionValues, $optionLabel)->toArray();
            }

            foreach($dataProvider->getNumericOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_NUMERIC][] = $this->getNumericAttributeSchema($optionValues, $optionLabel, null)->toArray();
            }

            foreach($dataProvider->getDateTimeOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_DATETIME][] = $this->getDatetimeAttributeSchema($optionValues, $optionLabel)->toArray();
            }
        }

        if(empty($content))
        {
            throw new NoRecordsFoundException("{$this->getLogProcessName()} : No records available. This is a logical exception in order to exit the handler loop.");
        }

        return $content;
    }

    /**
     * @return array
     */
    protected function getSchemaPropertyNames() : array
    {
        return $this->getPropertyHandlerSchema()->toList();
    }

    /**
     * @return DocPropertiesInterface
     */
    abstract public function getPropertyHandlerSchema() : DocPropertiesInterface;


}
