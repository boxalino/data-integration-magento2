<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Content;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\GenericDocHandlerTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocContentHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocContent;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DocHandler
 *
 * Generates the content for the doc_content document for a given account
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280968/doc+content
 *
 * The $propertyHandlers must be added by using the Generic sample
 *
 * @package Boxalino\DataIntegration\Service\Document\Content
 */
class DocHandler extends DocContent implements
    DocContentHandlerInterface,
    DocDeltaIntegrationInterface,
    DocMviewDeltaIntegrationInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface
{

    use GenericDocHandlerTrait;

}
