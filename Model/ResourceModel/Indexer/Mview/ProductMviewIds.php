<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Indexer\Mview;

use Boxalino\DataIntegration\Api\Mview\DiViewIdResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\EntityResourceTrait;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

/**
 * Declared as a dependency in the integration layer in case the mview is enabled
 */
class ProductMviewIds implements DiViewIdResourceInterface
{
    use EntityResourceTrait;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->adapter = $resource->getConnection();
    }

    /**
     * @param $ids
     * @param $websiteId
     * @return array
     */
    public function getAffectedIdsByMviewIdsWebsiteId($ids, $websiteId) : array
    {
        $mainSelect = $this->_getEntityByWebsiteIdSelect($websiteId);
        $select = $this->adapter->select()
            ->distinct(true)
            ->from(
                ['e' => new \Zend_Db_Expr("( " . $mainSelect->__toString() . " )")],
                ["e.entity_id"]
            )
            ->where("e.entity_id IN (?)", $ids);

        return $this->adapter->fetchCol($select);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getAffectedIdsByMviewIds(array $ids) : array
    {
        $defaultIds = $this->_getAffectedIdsByRelationConnection($ids);
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
    protected function _getAffectedIdsByRelationConnection(array $ids) : array
    {
        $select = $this->adapter->select()
            ->distinct(true)
            ->from(
                ['e' => $this->adapter->getTableName('catalog_product_entity')],
                ["e.entity_id"]
            )
            ->joinLeft(
                ['c_e' => new \Zend_Db_Expr("( ". $this->_getEntityIdsWithRelationsSelect()->__toString() . ' )')],
                "e.entity_id = c_e.entity_id",
                []
            )
            ->where("c_e.entity_id IN (?) OR c_e.as_parent IN (?) OR c_e.as_child IN (?) OR c_e.parent_id IN (?) OR c_e.child_id IN (?)", $ids);

        return $this->adapter->fetchCol($select);
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

    /**
     * USED FOR MVIEW
     *
     * @return Select
     */
    protected function _getEntityIdsWithRelationsSelect() : Select
    {
        return $this->_getEntityIdsWithRelationsBySelect(
            $this->adapter->select()->from($this->adapter->getTableName('catalog_product_entity'))
        );
    }


}
