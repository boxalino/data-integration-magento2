<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderItemPropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocOrderLineInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocSchemaTypedInterface;
use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Order\Product as OrderProductSchema;
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

                $content[$id][$this->getResolverType()][] = $schema;
                unset($schema);
            } catch (\Throwable $exception)
            {
                $this->logger->debug("Boxalino DI: Error on " . get_called_class() . " content export: " . $exception->getMessage());
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
            $id = $this->_getDocKey($item);
            $content[$id] = [];

            foreach($dataProvider->getStringOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_STRING][] = $this->getStringAttributeSchema($optionValues, $optionLabel);
            }

            foreach($dataProvider->getNumericOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_NUMERIC][] = $this->getNumericAttributeSchema($optionValues, $optionLabel, null);
            }

            foreach($dataProvider->getDateTimeOptions($item) as $optionLabel => $optionValues)
            {
                $content[$id][DocSchemaInterface::FIELD_DATETIME][] = $this->getDatetimeAttributeSchema($optionValues, $optionLabel);
            }

            foreach($propertyNames as $propertyName)
            {
                $propertyValue = $dataProvider->get($propertyName, $item);
                if(is_null($propertyValue))
                {
                    continue;
                }

                $content[$id][$propertyName] = $propertyValue;
            }
        }

        if(empty($content))
        {
            throw new NoRecordsFoundException("No records available. This is a logical exception in order to exit the handler loop.");
        }

        return $content;
    }

    /**
     * @return array
     */
    protected function getSchemaPropertyNames() : array
    {
        $schema = $this->getPropertyHandlerSchema();
        return array_keys($schema->toList());
    }

    /**
     * @return DocPropertiesInterface
     */
    abstract public function getPropertyHandlerSchema() : DocPropertiesInterface;
    

}
