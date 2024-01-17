<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Order\Contact as OrderContactSchema;

/**
 * Class Contact
 * Access the order billing and shipping information following the documented schema
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#CONTACT
 *
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
class Contact extends IntegrationPropertyHandlerAbstract
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
        return new OrderContactSchema();
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_CONTACTS;
    }


}
