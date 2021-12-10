<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;

/**
 * Interface used to declare the behavior for certain integration modes
 * A default is provided with the plugin and it is easy customizable by the integrator in the integration layer
 *
 * @package Boxalino\DataIntegration\Service\Document\Product
 */
interface ModeIntegratorInterface extends
    DocDeltaIntegrationInterface,
    DocSchemaPropertyHandlerInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface
{


}
