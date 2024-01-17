<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\UserGeneratedContent;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\GenericDocHandlerTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUserGeneratedContentHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUserGeneratedContent;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DocHandler
 *
 * Generates the content for the doc_user_generated_content document for a given account
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280985/doc+user+generated+content
 * This includes reviews, ratings, etc
 *
 * The $propertyHandlers must be added by using the Generic sample
 *
 * @package Boxalino\DataIntegration\Service\Document\UserGeneratedContent
 */
class DocHandler extends DocUserGeneratedContent implements
    DocUserGeneratedContentHandlerInterface,
    DocDeltaIntegrationInterface,
    DocMviewDeltaIntegrationInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface
{

    use GenericDocHandlerTrait;

}
