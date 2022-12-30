<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Product;

use Boxalino\DataIntegration\Service\Integration\Mode\Instant;
use Boxalino\DataIntegration\Service\Integration\Type\ProductTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocProductHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\ProductInstantIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

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

    public function __construct(
        DocProductHandlerInterface $productHandler,
        LoggerInterface $logger,
        array $docHandlers = [],
        int $timeout = 0,
        int $fullConversionThreshold = 0
    ){
        parent::__construct($logger, $docHandlers, $timeout, $fullConversionThreshold);

        $this->addHandler($productHandler);
    }


}
