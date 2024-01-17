<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\Order;

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
    public function _getValues(): array
    {
       return $this->getValuesForEntityTypedSchema();
    }

    /**
     * @return DocPropertiesInterface
     */
    public function getPropertyHandlerSchema() : DocPropertiesInterface
    {
        return new Order();
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "entity";
    }


}
