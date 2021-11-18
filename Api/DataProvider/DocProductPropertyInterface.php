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

    /**
     * @param string $code
     * @return DiSchemaDataProviderInterface
     */
    public function setAttributeCode(string $code) : DiSchemaDataProviderInterface;

    /**
     * @return string
     */
    public function getAttributeCode() : string;

    /**
     * @param int $id
     * @return DiSchemaDataProviderInterface
     */
    public function setAttributeId(int $id) : DiSchemaDataProviderInterface;

    /**
     * @return int
     */
    public function getAttributeId() : int;

}
