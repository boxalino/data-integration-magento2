<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Product;

use Boxalino\DataIntegration\Service\Integration\Mode\Full;
use Boxalino\DataIntegration\Service\Integration\Type\ProductTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\ProductIntegrationHandlerInterface;

/**
 * Class FullIntegrationHandler
 *
 * @package Boxalino\DataIntegrationDoc\Service
 */
class FullIntegrationHandler extends Full
    implements ProductIntegrationHandlerInterface
{

    use ProductTrait;

}
