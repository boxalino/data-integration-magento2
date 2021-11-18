<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderContactPropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocOrderItemPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderResolverInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Order\Product as OrderProductSchema;
use Psr\Log\LoggerInterface;

/**
 * Class Item
 * Access the order item information, following the documented schema
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252313666/doc+order
 *
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
class Item extends IntegrationPropertyHandlerAbstract
{

    public function getValues(): array
    {
        $content = [];
        /** @var DocOrderItemPropertyInterface $dataProvider */
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
                $schema = new OrderProductSchema();
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
            } catch (\Throwable $exception)
            {
                $this->logger->debug("Boxalino DI: Error on Order Products content export: " . $exception->getMessage());
            }
        }

        return $content;
    }

    /**
     * @return array
     */
    protected function getSchemaPropertyNames() : array
    {
        $schema = new OrderProductSchema();
        return array_keys($schema->toList());
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_PRODUCTS;
    }


}
