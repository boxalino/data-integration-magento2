<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderContactPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderResolverInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Order\Contact as OrderContactSchema;
use Psr\Log\LoggerInterface;

/**
 * Class Contact
 * Access the order billing and shipping information following the documented schema
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#CONTACT
 *
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
class Contact extends IntegrationPropertyHandlerAbstract
{

    public function getValues(): array
    {
        $content = [];
        /** @var DocOrderContactPropertyInterface $dataProvider */
        $dataProvider = $this->getDataProvider();
        $propertyNames = $this->getSchemaPropertyNames();
        foreach($dataProvider->getData() as $item)
        {
            $id = $this->_getDocKey($item);
            $content[$id] = [];

            try{
                $schema = new OrderContactSchema();
                foreach($propertyNames as $propertyName)
                {
                    $propertyValue = $dataProvider->get($propertyName, $item);
                    if(empty($propertyValue))
                    {
                        continue;
                    }
                    $schema->set($propertyName, $propertyValue);
                }

                $content[$id][$this->getResolverType()][] = $schema;
            } catch (\Throwable $exception)
            {
                $this->logger->debug("Boxalino DI: Error on Order Contacts content export: " . $exception->getMessage());
            }
        }

        return $content;
    }

    /**
     * @return array
     */
    protected function getSchemaPropertyNames() : array
    {
        $schema = new OrderContactSchema();
        return array_keys($schema->toList());
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_CONTACTS;
    }


}
