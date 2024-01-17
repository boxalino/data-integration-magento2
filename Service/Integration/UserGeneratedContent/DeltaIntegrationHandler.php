<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\UserGeneratedContent;

use Boxalino\DataIntegration\Service\Integration\Mode\Delta;
use Boxalino\DataIntegration\Service\Integration\Type\UserGeneratedContentTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUserGeneratedContentHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\UserGeneratedContentDeltaIntegrationHandlerInterface;
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
    implements UserGeneratedContentDeltaIntegrationHandlerInterface
{
    use UserGeneratedContentTrait;

    public function __construct(
        DocUserGeneratedContentHandlerInterface $docUserGeneratedContentHandler,
        LoggerInterface $logger,
        array $docHandlers = [],
        int $timeout = 0,
        int $fullConversionThreshold = 0
    ) {
        parent::__construct($logger, $docHandlers, $timeout, $fullConversionThreshold);

        $this->addHandler($docUserGeneratedContentHandler);
    }

}
