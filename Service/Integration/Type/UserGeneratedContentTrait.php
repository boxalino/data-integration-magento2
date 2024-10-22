<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Type;

use Boxalino\DataIntegrationDoc\Service\GcpRequestInterface;

/**
 * Class UserGeneratedContentTrait
 *
 * @package Boxalino\DataIntegrationDoc\Service
 */
trait UserGeneratedContentTrait
{

    /**
     * @return string
     */
    public function getIntegrationType(): string
    {
        return GcpRequestInterface::GCP_TYPE_USER_GENERATED_CONTENT;
    }


}
