<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\User;

use Boxalino\DataIntegration\Service\Integration\Mode\Full;
use Boxalino\DataIntegration\Service\Integration\Type\UserTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\UserIntegrationHandlerInterface;

/**
 * Class FullIntegrationHandler
 *
 * @package Boxalino\DataIntegrationDoc\Service
 */
class FullIntegrationHandler extends Full 
    implements UserIntegrationHandlerInterface
{
    use UserTrait;

}
