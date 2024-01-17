<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\UserOrderDocHandlerTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocOrder;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocOrderHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DocHandler
 * Generates the content for the doc_order document
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252313666/doc+order
 *
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
class DocHandler extends DocOrder implements
    DocOrderHandlerInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface,
    DocDeltaIntegrationInterface,
    DocMviewDeltaIntegrationInterface
{

    use UserOrderDocHandlerTrait;


}
