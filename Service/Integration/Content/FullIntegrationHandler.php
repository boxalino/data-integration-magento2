<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Content;

use Boxalino\DataIntegration\Service\Integration\Mode\Full;
use Boxalino\DataIntegration\Service\Integration\Type\ContentTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocContentHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\ContentIntegrationHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocLanguagesHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class FullIntegrationHandler
 *
 * @package Boxalino\DataIntegration\Service\Integration\Content
 */
class FullIntegrationHandler extends Full
    implements ContentIntegrationHandlerInterface
{
    use ContentTrait;

    public function __construct(
        DocContentHandlerInterface $docContentHandler,
        DocLanguagesHandlerInterface $languagesHandler,
        LoggerInterface $logger,
        array $docHandlers = [],
        int $timeout = 0,
        int $fullConversionThreshold = 0
    ) {
        parent::__construct($logger, $docHandlers, $timeout, $fullConversionThreshold);

        $this->addHandler($languagesHandler);
        $this->addHandler($docContentHandler);
    }


}
