<?php
namespace Boxalino\DataIntegration\Api\Mview;

/**
 * Interface DiViewIdResourceInterface
 * Resource to access the content ids affected by mview changes
 */
interface DiViewIdResourceInterface
{

    /**
     * @param $ids
     * @param $websiteId
     * @return array
     */
    public function getIdsByMviewIdsWebsiteId($ids, $websiteId) : array;

    /**
     * @param array $ids
     * @return array
     */
    public function getAffectedIdsByMviewIds(array $ids) : array;


}
