<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\User;

use Boxalino\DataIntegration\Service\Integration\Mode\Delta;
use Boxalino\DataIntegration\Service\Integration\Type\UserTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\UserDeltaIntegrationHandlerInterface;

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
    implements UserDeltaIntegrationHandlerInterface
{
    use UserTrait;

}
