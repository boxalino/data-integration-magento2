<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Order;

use Magento\Framework\DB\Select;

/**
 * Helper trait for accessing eav content
 * (joins, selects, etc)
 */
trait EntityResourceTrait
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @param array $storeIds
     * @return array
     */
    public function getEntityByStoreIds(array $storeIds): array
    {
        $select = $this->adapter->select()
            ->from(
                ['s_o' => $this->adapter->getTableName('sales_order')],
                ["*"]
            )
            ->where("s_o.store_id IN (?) " , $storeIds)
            ->orWhere("s_o.store_id = 0");

        return $this->adapter->fetchAll($select);
    }

    /**
     * @param array $storeIds
     * @return Select
     */
    public function getEntityByStoreIdsSelect(array $storeIds): Select
    {
        $select = $this->adapter->select()
            ->from(
                ['s_o' => $this->adapter->getTableName('sales_order')],
                ["*"]
            )
            ->where("s_o.store_id IN (?) " , $storeIds)
            ->orWhere("s_o.store_id = 0");

        return $select;
    }


}
