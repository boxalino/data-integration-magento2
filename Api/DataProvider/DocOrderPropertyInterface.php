<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;

/**
 * Interface DocOrderPropertyInterface
 * doc_order properties data handler
 */
interface DocOrderPropertyInterface extends DiSchemaDataProviderInterface,
    DocSchemaTypedInterface,
    DocDeltaIntegrationInterface,
    DocMviewDeltaIntegrationInterface,
    DiHandlerIntegrationConfigurationInterface
{

    
}
