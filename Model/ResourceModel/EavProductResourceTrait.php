<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel;

use Magento\Framework\DB\Select;

/**
 * Helper trait for accessing product-related eav content
 * (joins, selects, etc)
 */
trait EavProductResourceTrait
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @param array $fields
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllEntityByFieldsWebsite(array $fields, string $websiteId) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            );

        return $this->adapter->fetchAll($select);
    }

    /**
     * Default function for accessing product attributes values
     * join them with default store
     * and make a selection on the store id
     *
     * @param int $attributeId
     * @param int $storeId
     * @param string $table
     * @param string $main
     * @return Select
     */
    protected function getEavJoinAttributeSQLByStoreAttrIdTable(int $attributeId, int $storeId, string $table, string $main = 'catalog_product_entity') : Select
    {
        $select = $this->_getEavJoin($attributeId, $storeId, $table, $main);
        return $this->adapter->select()
            ->from(
                ['joins' => $select],
                [
                    'attribute_id' => 'joins.attribute_id',
                    'entity_id' => 'joins.entity_id',
                    'store_id' => new \Zend_Db_Expr("IF (joins.store_value IS NULL OR CAST(joins.store_value AS char) = '', 0, joins.store_id)"),
                    'value' => new \Zend_Db_Expr("IF (joins.store_value IS NULL OR CAST(joins.store_value AS char) = '', joins.default_value, joins.store_value)")
                ]
            );
    }

    /**
     * Default function for accessing product attributes values
     * join them with default store
     * and make a selection on the website (ex: for global or website scoped attributes)
     *
     * @param int $attributeId
     * @param array $storeIds
     * @param string $table
     * @param string $main
     * @return Select
     */
    protected function getEavJoinAttributeSQLByStoresAttrIdTable(int $attributeId, array $storeIds, string $table, string $main = 'catalog_product_entity') : Select
    {
        $select = $this->_getEavJoin($attributeId, $storeIds, $table, $main);
        return $this->adapter->select()
            ->from(
                ['joins' => $select],
                [
                    'attribute_id'=>'joins.attribute_id',
                    'entity_id' => 'joins.entity_id',
                    'value' => new \Zend_Db_Expr("IF (joins.store_value IS NULL OR CAST(joins.store_value AS char) = '', joins.default_value, joins.store_value)")
                ]
            );
    }

    /**
     * @param int $attributeId
     * @param $storeId
     * @param string $table
     * @param string $main
     * @return Select
     */
    protected function _getEavJoin(int $attributeId, $storeId, string $table, string $main = 'catalog_product_entity') : Select
    {
        $select = $this->adapter
            ->select()
            ->from(
                ['e' => $main],
                ['entity_id' => 'entity_id']
            );

        $innerCondition = [
            $this->adapter->quoteInto("{$attributeId}_default.entity_id = e.entity_id", ''),
            $this->adapter->quoteInto("{$attributeId}_default.attribute_id = ?", $attributeId),
            $this->adapter->quoteInto("{$attributeId}_default.store_id = ?", 0)
        ];

        if(is_array($storeId))
        {
            $storeId = $this->adapter->quote($storeId);
        }
        $joinLeftConditions = [
            $this->adapter->quoteInto("{$attributeId}_store.entity_id = e.entity_id", ''),
            $this->adapter->quoteInto("{$attributeId}_store.attribute_id = ?", $attributeId),
            $this->adapter->quoteInto("{$attributeId}_store.store_id IN(?)", $storeId)
        ];

        $select
            ->joinInner(
                [$attributeId . '_default' => $table], implode(' AND ', $innerCondition),
                ['default_value' => 'value', 'attribute_id']
            )
            ->joinLeft(
                ["{$attributeId}_store" => $table], implode(' AND ', $joinLeftConditions),
                ["store_value" => 'value', 'store_id']
            );

        return $select;
    }


}
