<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;

/**
 * Interface DocProductPropertyInterface
 * doc_product properties data handler
 */
interface DocProductPropertyInterface extends DiSchemaDataProviderInterface,
    DocDeltaIntegrationInterface,
    DiHandlerIntegrationConfigurationInterface
{

    public function setAttributeCode(string $code) : DocProductPropertyInterface;

    public function getAttributeCode() : string;

    public function setAttributeId(int $id) : DocProductPropertyInterface;

    public function getAttributeId() : int;

    public function getDataForAttribute() : array;

}
