<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Indexer\Mview;

use Boxalino\DataIntegration\Api\Mview\DiViewIdResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\EntityResourceTrait;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

/**
 * Declared as a dependency in the integration layer in case the mview is enabled
 * Can be used for delta & instant updates as resource for which IDs to be updated
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
    public function getIdsByMviewIdsWebsiteId($ids, $websiteId) : array
    {
        $mainSelect = $this->_getProductEntityByWebsiteIdSelect($websiteId);
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
