<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\User;

use Boxalino\DataIntegration\Service\Document\DiIntegrateTypedSchemaTrait;
use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\User\Contact as UserContactSchema;

/**
 * Class Contact
 * Access the user contact data (billing and shipping) information following the documented schema
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#CONTACT
 *
 * @package Boxalino\DataIntegration\Service\Document\User
 */
class Contact extends IntegrationPropertyHandlerAbstract
{

    use DiIntegrateTypedSchemaTrait;

    public function _getValues(): array
    {
        return $this->getValuesForTypedSchema();
    }

    /**
     * @return DocPropertiesInterface
     */
    public function getPropertyHandlerSchema() : DocPropertiesInterface
    {
        return new UserContactSchema();
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_CONTACTS;
    }


}
