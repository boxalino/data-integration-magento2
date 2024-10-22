<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Type;

use Boxalino\DataIntegrationDoc\Service\GcpRequestInterface;
use Magento\Sales\Api\Data\TransactionInterface;

/**
 * Class ContentTrait
 *
 * @package Boxalino\DataIntegrationDoc\Service
 */
trait ContentTrait
{

    /**
     * @return string
     */
    public function getIntegrationType(): string
    {
        return GcpRequestInterface::GCP_TYPE_CONTENT;
    }


}
