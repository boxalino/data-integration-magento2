<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\UserSelection;

use Boxalino\DataIntegration\Service\Integration\Mode\Delta;
use Boxalino\DataIntegration\Service\Integration\Type\UserSelectionTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUserSelectionHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\UserSelectionDeltaIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

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
    implements UserSelectionDeltaIntegrationHandlerInterface
{
    use UserSelectionTrait;

    public function __construct(
        DocUserSelectionHandlerInterface $docUserSelectionHandler,
        LoggerInterface $logger,
        array $docHandlers = [],
        int $timeout = 0,
        int $fullConversionThreshold = 0
    ) {
        parent::__construct($logger, $docHandlers, $timeout, $fullConversionThreshold);

        $this->addHandler($docUserSelectionHandler);
    }

}
