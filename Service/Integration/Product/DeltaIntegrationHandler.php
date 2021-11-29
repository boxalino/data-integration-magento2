<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Product;

use Boxalino\DataIntegration\Service\Integration\Mode\Delta;
use Boxalino\DataIntegration\Service\Integration\Type\ProductTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\ProductDeltaIntegrationHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\ProductInstantIntegrationHandlerInterface;

/**
 * Class DeltaIntegrationHandler
 * Handles the product integration scenarios:
 * - delta
 *
 * Integrated as a service
 *
 * @package Boxalino\DataIntegrationDoc\Service\Integration\Order
 */
class DeltaIntegrationHandler extends Delta
    implements ProductDeltaIntegrationHandlerInterface
{
    use ProductTrait;

}
