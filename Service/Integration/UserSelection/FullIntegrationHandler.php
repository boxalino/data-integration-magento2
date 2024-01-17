<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\UserSelection;

use Boxalino\DataIntegration\Service\Integration\Mode\Full;
use Boxalino\DataIntegration\Service\Integration\Type\UserSelectionTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUserSelectionHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\UserSelectionIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class FullIntegrationHandler
 *
 * @package Boxalino\DataIntegrationDoc\Service
 */
class FullIntegrationHandler extends Full
    implements UserSelectionIntegrationHandlerInterface
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
