<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\UserGeneratedContent;

use Boxalino\DataIntegration\Service\Integration\Mode\Full;
use Boxalino\DataIntegration\Service\Integration\Type\UserGeneratedContentTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUserGeneratedContentHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\UserGeneratedContentIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class FullIntegrationHandler
 *
 * @package Boxalino\DataIntegrationDoc\Service
 */
class FullIntegrationHandler extends Full
    implements UserGeneratedContentIntegrationHandlerInterface
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
