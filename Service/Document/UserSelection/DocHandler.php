<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\UserSelection;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\GenericDocHandlerTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUserSelectionHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUserSelection;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DocHandler
 *
 * Generates the content for the doc_user_selection document for a given account
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252313673/doc+user+selection
 * This includes wishlist, baskets, etc
 *
 * The $propertyHandlers must be added by using the Generic sample
 *
 * @package Boxalino\DataIntegration\Service\Document\UserSelection
 */
class DocHandler extends DocUserSelection implements
    DocUserSelectionHandlerInterface,
    DocDeltaIntegrationInterface,
    DocMviewDeltaIntegrationInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface
{

    use GenericDocHandlerTrait;

}
