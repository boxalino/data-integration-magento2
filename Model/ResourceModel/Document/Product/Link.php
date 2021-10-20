<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Magento\Framework\DB\Select;

/**
 * Class Link
 * Exporter for the seo_url property
 * The Shopware6 SEO property matches the "link" doc_product schema property
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Link extends ModeIntegrator
{

    /**
     * Export the SEO URL link based on product visibility and parent
     *
     * @return Select
     */
    public function getSeoUrlInformationByStoreId(int $storeId) : Select
    {
        $urlKeyAttrId = $this->getAttributeIdByAttributeCodeAndEntityType("url_key", \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        $urlKeySql = $this->getEavJoinAttributeSQLByStoreAttrIdTable($urlKeyAttrId, $storeId, "catalog_product_entity_varchar");

        $visibilityId = $this->getAttributeIdByAttributeCodeAndEntityType('visibility', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        $visibilitySql = $this->getEavJoinAttributeSQLByStoreAttrIdTable($visibilityId, $storeId, "catalog_product_entity_int");
        $visibilityOptions = implode(',', [\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH, \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH]);

        $select = $this->adapter->select()
            ->from(
                ['c_p_e' => $this->adapter->getTableName('catalog_product_entity')],
                ['c_p_e.entity_id']
            )
            ->joinLeft(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                'c_p_e.entity_id = c_p_r.child_id',
                ['c_p_r.parent_id']
            )
            ->joinLeft(
                ['c_p_e_u' => new \Zend_Db_Expr("( ". $urlKeySql->__toString() . ' )')],
                "c_p_e.entity_id = c_p_e_u.entity_id",
                ['entity_value'=>'c_p_e_u.value', 'entity_store_id' => 'c_p_e_u.store_id']
            )
            ->joinLeft(
                ['c_p_e_u_p' => new \Zend_Db_Expr("( ". $urlKeySql->__toString() . ' )')],
                "c_p_r.parent_id = c_p_e_u_p.entity_id",
                ['parent_value'=>'c_p_e_u_p.value']
            )
            ->joinLeft(
                ['c_p_e_v' => new \Zend_Db_Expr("( ". $visibilitySql->__toString() . ' )')],
                "c_p_e.entity_id = c_p_e_v.entity_id",
                ['entity_visibility'=>'c_p_e_v.value']
            );

//        if(!empty($this->exportIds) && $this->isDelta)
//        {
//            $select->where('c_p_e.entity_id IN(?)', $this->exportIds);
//        }

        $finalSelect = $this->adapter->select()
            ->from(
                ["entity_select" => new \Zend_Db_Expr("( ". $select->__toString() . " )")],
                [
                    "entity_select.entity_id",
                    "store_id" => "entity_select.entity_store_id",
                    "value" => new \Zend_Db_Expr("
                        (CASE
                            WHEN entity_select.parent_id IS NULL THEN entity_select.entity_value
                            WHEN entity_select.entity_visibility IN ({$visibilityOptions}) THEN entity_select.entity_value
                            ELSE entity_select.parent_value
                         END
                        )"
                    )
                ]
            );

        return $finalSelect;
    }

    /**
     * @return Select
     */
    public function getParentSeoUrlInformationByStoreId(int $storeId) : Select
    {
        $urlKeyAttrId = $this->getAttributeIdByAttributeCodeAndEntityType("url_key", \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        $urlKeySql = $this->getEavJoinAttributeSQLByStoreAttrIdTable($urlKeyAttrId, $storeId, "catalog_product_entity_varchar");

        $select = $this->adapter->select()
            ->from(
                ['c_p_e' => $this->adapter->getTableName('catalog_product_entity')],
                ['c_p_e.entity_id']
            )
            ->joinLeft(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                'c_p_e.entity_id = c_p_r.child_id',
                ['c_p_r.parent_id']
            )
            ->joinLeft(
                ['c_p_e_u' => new \Zend_Db_Expr("( ". $urlKeySql->__toString() . ' )')],
                "c_p_e.entity_id = c_p_e_u.entity_id",
                ['entity_value'=>'c_p_e_u.value', 'entity_store_id' => 'c_p_e_u.store_id']
            )
            ->joinLeft(
                ['c_p_e_u_p' => new \Zend_Db_Expr("( ". $urlKeySql->__toString() . ' )')],
                "c_p_r.parent_id = c_p_e_u_p.entity_id",
                ['parent_value'=>'c_p_e_u_p.value']
            );

//        if(!empty($this->exportIds) && $this->isDelta)
//        {
//            $select->where('c_p_e.entity_id IN(?)', $this->exportIds);
//        }

        $finalSelect = $this->adapter->select()
            ->from(
                ["entity_select" => new \Zend_Db_Expr("( ". $select->__toString() . " )")],
                [
                    "entity_select.entity_id",
                    "value" => new \Zend_Db_Expr("
                        (CASE
                            WHEN entity_select.parent_id IS NULL THEN entity_select.entity_value
                            ELSE entity_select.parent_value
                         END
                        )"
                    )
                ]
            );

        return $finalSelect;
    }

}
