<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavProductResourceTrait;
use Magento\Framework\DB\Select;

/**
 * Class Link
 * Exporter for the link property
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Link extends ModeIntegrator
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
        $mainEntitySelect = $this->getUrlKeyInformationByStoreId($websiteId, $storeId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            );

        return $this->adapter->fetchPairs($select);
    }

    /**
     * Export the SEO URL link based on product visibility and parent
     *
     * @return Select
     */
    public function getUrlKeyInformationByStoreId(string $websiteId, int $storeId) : Select
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);

        $urlKeyAttrId = $this->getAttributeIdByAttributeCodeAndEntityTypeId("url_key", \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        $urlKeySql = $this->getEavJoinAttributeSQLByStoreAttrIdTable((int)$urlKeyAttrId, $storeId, "catalog_product_entity_varchar");

        $visibilityId = $this->getAttributeIdByAttributeCodeAndEntityTypeId('visibility', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        $visibilitySql = $this->getEavJoinAttributeSQLByStoreAttrIdTable((int) $visibilityId, $storeId, "catalog_product_entity_int");

        $visibilityOptions = implode(',',
            [
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH
            ]
        );

        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                ['c_p_e_s.entity_id']
            )
            ->joinLeft(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                'c_p_e_s.entity_id = c_p_r.child_id',
                ['c_p_r.parent_id']
            )
            ->joinLeft(
                ['c_p_e_u' => new \Zend_Db_Expr("( ". $urlKeySql->__toString() . ' )')],
                "c_p_e_s.entity_id = c_p_e_u.entity_id",
                ['entity_value'=>'c_p_e_u.value', 'entity_store_id' => 'c_p_e_u.store_id']
            )
            ->joinLeft(
                ['c_p_e_u_p' => new \Zend_Db_Expr("( ". $urlKeySql->__toString() . ' )')],
                "c_p_r.parent_id = c_p_e_u_p.entity_id",
                ['parent_value'=>'c_p_e_u_p.value']
            )
            ->joinLeft(
                ['c_p_e_v' => new \Zend_Db_Expr("( ". $visibilitySql->__toString() . ' )')],
                "c_p_e_s.entity_id = c_p_e_v.entity_id",
                ['entity_visibility'=>'c_p_e_v.value']
            );

        $finalSelect = $this->adapter->select()
            ->from(
                ["entity_select" => new \Zend_Db_Expr("( ". $select->__toString() . " )")],
                [
                    "entity_select.entity_id",
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


}
