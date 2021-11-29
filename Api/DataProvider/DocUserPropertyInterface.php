<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationInterface;

/**
 * Interface DocUserPropertyInterface
 * Handling the main user information for the doc_user line
 */
interface DocUserPropertyInterface extends DiSchemaDataProviderInterface, 
    DocDeltaIntegrationInterface,
    DocMviewDeltaIntegrationInterface,
    DiHandlerIntegrationConfigurationInterface,
    DocSchemaContactInterface,
    DocSchemaTypedInterface
{

}
