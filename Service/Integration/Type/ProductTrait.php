<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Type;

use Boxalino\DataIntegrationDoc\Service\GcpRequestInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

/**
 * Class IntegrationTypeTrait
 *
 * @package Boxalino\DataIntegrationDoc\Service
 */
trait ProductTrait
{

    /**
     * @return string
     */
    public function getIntegrationType(): string
    {
        return GcpRequestInterface::GCP_TYPE_PRODUCT;
    }


}
