<?php
namespace Boxalino\DataIntegration\Api\Mode;

use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;

/**
 * Interface DocMviewDeltaIntegrationInterface
 */
interface DocMviewDeltaIntegrationInterface extends DocDeltaIntegrationInterface
{

    /**
     * @param array $ids
     * @return DocDeltaIntegrationInterface
     */
    public function setMviewIds(array $ids) : DocDeltaIntegrationInterface;

    /**
     * @return array
     */
    public function getIds() : array;

    
}
