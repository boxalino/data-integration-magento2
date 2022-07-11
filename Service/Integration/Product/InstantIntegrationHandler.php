<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Product;

use Boxalino\DataIntegration\Service\Integration\Mode\Instant;
use Boxalino\DataIntegration\Service\Integration\Type\ProductTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\ProductInstantIntegrationHandlerInterface;

/**
 * Class InstantIntegrationHandler
 * Handles the product integration scenarios:
 * - instant
 *
 * Integrated as a service
 *
 * @package Boxalino\DataIntegrationDoc\Service\Integration\Order
 */
class InstantIntegrationHandler extends Instant
    implements ProductInstantIntegrationHandlerInterface
{
    use ProductTrait;

}
