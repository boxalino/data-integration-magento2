<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavProductResourceTrait;
use Magento\Framework\DB\Select;

/**
 * Class Status
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Status extends ModeIntegrator
{
    use EavAttributeResourceTrait;
    use EavProductResourceTrait;

    /**
     * @param array $fields
     * @param string $websiteId
     * @param int $storeId
     * @return array
     */
    public function getFetchPairsByFieldsWebsiteStore(array $fields, string $websiteId, int $storeId) : array
    {
        $mainEntitySelect = $this->getStatusParentDependabilityByStore($websiteId, $storeId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            );

        return $this->adapter->fetchPairs($select);
    }

    /**
     * Query for setting the product status value based on the parent properties and product visibility
     * Fixes the issue when parent product is enabled but child product is disabled.
     *
     * @param string $websiteId
     * @param int $storeId
     * @return \Magento\Framework\DB\Select
     */
    public function getStatusParentDependabilityByStore(string $websiteId, int $storeId) : Select
    {
        $statusId = (int)$this->getAttributeIdByAttributeCodeAndEntityTypeId('status', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        $visibilityId = (int)$this->getAttributeIdByAttributeCodeAndEntityTypeId('visibility', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);

        $parentsCountSql = $this->getAttributeParentCountSqlByAttrIdValueStoreId($websiteId, $statusId,  \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED, $storeId);
        $childCountSql = $this->getParentAttributeChildCountSqlByAttrIdValueStoreId($websiteId, $statusId,  \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED, $storeId);

        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $statusSql = $this->getEavJoinAttributeSQLByStoreAttrIdTable($statusId, $storeId, "catalog_product_entity_int");
        $visibilitySql = $this->getEavJoinAttributeSQLByStoreAttrIdTable($visibilityId, $storeId, "catalog_product_entity_int");
        $select = $this->adapter->select()
            ->from(
                ['c_p_e' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
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

        $configurableType = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
        $groupedType = \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE;
        $visibilityOptions = implode(',', [
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH]
        );
        $finalSelect = $this->adapter->select()
            ->from(
                ["entity_select" => new \Zend_Db_Expr("( ". $select->__toString() . " )")],
                [
                    "entity_select.entity_id",
                    "entity_select.parent_id",
                    "entity_select.store_id",
                    "value" => "entity_select.entity_status",
                    "contextual" => new \Zend_Db_Expr("
                        (CASE
                            WHEN (entity_select.type_id = '{$configurableType}' OR entity_select.type_id = '{$groupedType}') AND entity_select.entity_status = '1' THEN IF(child_count.child_count > 0, 1, 2)
                            WHEN entity_select.parent_id IS NULL THEN entity_select.entity_status
                            WHEN entity_select.entity_status = '2' THEN 2
                            WHEN entity_select.entity_status = '1' AND entity_select.entity_visibility IN ({$visibilityOptions}) THEN 1
                            ELSE IF(entity_select.entity_status = '1' AND (parent_count.count > 0 OR parent_count.count IS NOT NULL), 1, 2)
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

    /**
     * Getting count of parent products that have a certain value for an attribute
     * Used for validation of child values
     * 
     * @param string $websiteId
     * @param int $attributeId
     * @param int $value
     * @param int $storeId
     * @return Select
     */
    protected function getAttributeParentCountSqlByAttrIdValueStoreId(string $websiteId, int $attributeId, int $value, int $storeId) : \Magento\Framework\DB\Select
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $storeAttributeValue = $this->getEavJoinAttributeSQLByStoreAttrIdTable($attributeId, $storeId, "catalog_product_entity_int");
        $select = $this->adapter->select()
            ->from(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                ['c_p_r.parent_id']
            )
            ->joinLeft(
                ['c_p_e' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                'c_p_e.entity_id = c_p_r.child_id',
                ['c_p_e.entity_id']
            )
            ->join(['t_d' => new \Zend_Db_Expr("( ". $storeAttributeValue->__toString() . ' )')],
                't_d.entity_id = c_p_r.parent_id',
                ['t_d.value']
            );

        return $this->adapter->select()
            ->from(
                ["parent_select"=> new \Zend_Db_Expr("( ". $select->__toString() . ' )')],
                ["count" => new \Zend_Db_Expr("COUNT(parent_select.parent_id)"), 'entity_id']
            )
            ->where("parent_select.value = ?", $value)
            ->group("parent_select.entity_id");
    }

    /**
     * Getting count of child products that have a certain value for an attribute
     * Used for validation of parent values
     *
     * @param string $websiteId
     * @param int $attributeId
     * @param int $value
     * @param int $storeId
     * @return Select
     */
    protected function getParentAttributeChildCountSqlByAttrIdValueStoreId(string $websiteId, int $attributeId, int $value, int $storeId) : Select
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $storeAttributeValue = $this->getEavJoinAttributeSQLByStoreAttrIdTable($attributeId, $storeId, "catalog_product_entity_int");
        $select = $this->adapter->select()
            ->from(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                ['c_p_r.child_id']
            )
            ->joinLeft(
                ['c_p_e' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                'c_p_e.entity_id = c_p_r.parent_id',
                ['c_p_e.entity_id']
            )
            ->join(['t_d' => new \Zend_Db_Expr("( ". $storeAttributeValue->__toString() . ' )')],
                't_d.entity_id = c_p_r.child_id',
                ['t_d.value']
            )
            ->where('t_d.value = ?', $value);

        return $this->adapter->select()
            ->from(
                ["child_select"=> new \Zend_Db_Expr("( ". $select->__toString() . ' )')],
                ["child_count" => new \Zend_Db_Expr("COUNT(child_select.child_id)"), 'entity_id']
            )
            ->group("child_select.entity_id");
    }


}
