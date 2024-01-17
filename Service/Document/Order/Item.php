<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Order\Product as OrderProductSchema;

/**
 * Class Item
 * Access the order item information, following the documented schema
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252313666/doc+order
 *
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
class Item extends IntegrationPropertyHandlerAbstract
{

    public function _getValues(): array
    {
        return $this->getValuesForTypedSchema();
    }

    /**
     * @return DocPropertiesInterface
     */
    public function getPropertyHandlerSchema() : DocPropertiesInterface
    {
        return new OrderProductSchema();
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_PRODUCTS;
    }


}
