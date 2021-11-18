<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderLineInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderResolverInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\NoRecordsFoundException;
use Psr\Log\LoggerInterface;

/**
 * Class Entity
 * Access the order main information for the order entity
 *
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
class Entity extends IntegrationPropertyHandlerAbstract
{

    /**
     * @return array
     */
    public function getValues(): array
    {
        $content = [];
        /** @var DocOrderLineInterface $dataProvider */
        $dataProvider = $this->getDataProvider();
        $propertyNames = array_merge(
            $this->getOrderSingleValueSchemaTypes(),
            $this->getOrderBooleanSchemaTypes(),
            $this->getOrderNumericSchemaTypes()
        );
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

                $content[$id][$propertyName] = $dataProvider->get($propertyName, $item);
            }
        }

        if(empty($content))
        {
            throw new NoRecordsFoundException("No records available. This is a logical exception in order to exit the handler loop.");
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "entity";
    }


}
