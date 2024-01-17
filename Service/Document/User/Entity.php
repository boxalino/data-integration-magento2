<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\User;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\User;

/**
 * Class Entity
 * Access the customer main information for the doc_user content
 *
 * @package Boxalino\DataIntegration\Service\Document\User
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
        return new User();
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "entity";
    }


}
