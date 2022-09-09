<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Indexer\Mview;

use Boxalino\DataIntegration\Api\Mview\DiViewIdResourceInterface;

/**
 *
 */
class ProductParentMviewIdsWithSuperLink extends ProductMviewIdsWithSuperLink
    implements DiViewIdResourceInterface
{
    /**
     * @param array $ids
     * @return array
     */
    public function getAffectedIdsByMviewIds(array $ids) : array
    {
        $defaultIds = parent::getAffectedIdsByMviewIds($ids);
        $superLinkIds = [];

        if(count($defaultIds))
        {
            $superLinkIds = $this->_getAffectedIdsBySuperLinkConnection($defaultIds);
        }

        return array_merge($defaultIds, $superLinkIds);
    }

    /**
     * @param array $ids
     * @return array
     */
    protected function _getAffectedIdsBySuperLinkConnection(array $ids) : array
    {
        $select = $this->adapter->select()
            ->distinct(true)
            ->from(
                ['c_p_s_l' => $this->adapter->getTableName('catalog_product_super_link')],
                ["c_p_s_l.product_id"]
            )
            ->where("c_p_s_l.parent_id IN (?)", $ids);

        return $this->adapter->fetchCol($select);
    }


}
