<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\User;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\UserOrderDocHandlerTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUser;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUserHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\FullIntegrationInterface;

/**
 * Class DocHandler
 * Generates the content for the doc_user document
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252182638/doc_user
 *
 * @package Boxalino\DataIntegration\Service\Document\User
 */
class DocHandler extends DocUser implements
    DocUserHandlerInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface,
    DocDeltaIntegrationInterface,
    DocMviewDeltaIntegrationInterface
{

    use UserOrderDocHandlerTrait;


}
