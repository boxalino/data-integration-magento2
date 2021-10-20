<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

/**
 * Class Status
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Status extends ModeIntegrator
{
    /**
     * Query for setting the product status value based on the parent properties and product visibility
     * Fixes the issue when parent product is enabled but child product is disabled.
     *
     * @param int $storeId
     * @return \Magento\Framework\DB\Select
     */
    public function getStatusParentDependabilityByStore(int $storeId) : \Magento\Framework\DB\Select
    {
        $statusId = $this->getAttributeIdByAttributeCodeAndEntityType('status', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        $visibilityId = $this->getAttributeIdByAttributeCodeAndEntityType('visibility', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);

        $parentsCountSql = $this->getAttributeParentCountSqlByAttrIdValueStoreId($statusId,  \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED, $storeId);
        $childCountSql = $this->getParentAttributeChildCountSqlByAttrIdValueStoreId($statusId,  \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED, $storeId);

        $statusSql = $this->getEavJoinAttributeSQLByStoreAttrIdTable($statusId, $storeId, "catalog_product_entity_int");
        $visibilitySql = $this->getEavJoinAttributeSQLByStoreAttrIdTable($visibilityId, $storeId, "catalog_product_entity_int");
        $select = $this->adapter->select()
            ->from(
                ['c_p_e' => $this->adapter->getTableName('catalog_product_entity')],
                ['c_p_e.entity_id', 'c_p_e.type_id']
            )
            ->joinLeft(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                'c_p_e.entity_id = c_p_r.child_id',
                ['parent_id']
            )
            ->join(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $statusSql->__toString() . ' )')],
                "c_p_e.entity_id = c_p_e_s.entity_id",
                ['c_p_e_s.attribute_id', 'c_p_e_s.store_id','entity_status'=>'c_p_e_s.value']
            )
            ->join(
                ['c_p_e_v' => new \Zend_Db_Expr("( ". $visibilitySql->__toString() . ' )')],
                "c_p_e.entity_id = c_p_e_v.entity_id",
                ['entity_visibility'=>'c_p_e_v.value']
            );

//        if(!empty($this->exportIds) && $this->isDelta) $select->where('c_p_e.entity_id IN(?)', $this->exportIds);
        $configurableType = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
        $groupedType = \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE;
        $visibilityOptions = implode(',', [\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH, \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG, \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH]);
        $finalSelect = $this->adapter->select()
            ->from(
                ["entity_select" => new \Zend_Db_Expr("( ". $select->__toString() . " )")],
                [
                    "entity_select.entity_id",
                    "entity_select.parent_id",
                    "entity_select.store_id",
                    "value" => new \Zend_Db_Expr("
                        (CASE
                            WHEN (entity_select.type_id = '{$configurableType}' OR entity_select.type_id = '{$groupedType}') AND entity_select.entity_status = '1' THEN IF(child_count.child_count > 0, 1, 2)
                            WHEN entity_select.parent_id IS NULL THEN entity_select.entity_status
                            WHEN entity_select.entity_status = '2' THEN 2
                            WHEN entity_select.entity_status = '1' AND entity_select.entity_visibility IN ({$visibilityOptions}) AND entity_select.parent_id IS NOT NULL AND parent_count.count IS NULL THEN 2
                            ELSE IF(entity_select.entity_status = '1' AND entity_select.entity_visibility IN ({$visibilityOptions}), 1, IF(entity_select.entity_status = '1' AND (parent_count.count > 0 OR parent_count.count IS NOT NULL), 1, 2))
                         END
                        )"
                    )
                ]
            )
            ->joinLeft(
                ["parent_count"=> new \Zend_Db_Expr("( ". $parentsCountSql->__toString() . " )")],
                "parent_count.entity_id = entity_select.entity_id",
                ["count"]
            )
            ->joinLeft(
                ["child_count"=> new \Zend_Db_Expr("( ". $childCountSql->__toString() . " )")],
                "child_count.entity_id = entity_select.entity_id",
                ["child_count"]
            );

        return $finalSelect;
    }

}
