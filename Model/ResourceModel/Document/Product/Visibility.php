<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavProductResourceTrait;
use Magento\Framework\DB\Select;

/**
 * Class Visibility
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Visibility extends ModeIntegrator
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
        $mainEntitySelect = $this->getVisibilityParentUnionSqlByCodeTypeStore($websiteId, $storeId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            );

        return $this->adapter->fetchPairs($select);
    }

    /**
     * Get child product attribute value based on the parent product attribute value
     *
     * @param string $websiteId
     * @param int $storeId
     * @param string $type
     * @return Select
     */
    public function getVisibilityParentUnionSqlByCodeTypeStore(string $websiteId, int $storeId, string $type = "int") : Select
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $visibilityId = $this->getAttributeIdByAttributeCodeAndEntityTypeId("visibility", \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        $visibilitySql = $this->getEavJoinAttributeSQLByStoreAttrIdTable((int)$visibilityId, $storeId, "catalog_product_entity_int");

        return $this->adapter->select()
            ->from(
                ['c_p_e' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                ['c_p_e.entity_id', 'c_p_e.type_id']
            )
            ->joinLeft(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                'c_p_e.entity_id = c_p_r.child_id',
                ['parent_id']
            )
            ->joinLeft(
                ['c_p_e_v' => new \Zend_Db_Expr("( ". $visibilitySql->__toString() . ' )')],
                "c_p_e.entity_id = c_p_e_v.entity_id",
                ['entity_value'=>'c_p_e_v.value']
            )
            ->joinLeft(
                ['c_p_e_v_p' => new \Zend_Db_Expr("( ". $visibilitySql->__toString() . ' )')],
                "c_p_r.parent_id = c_p_e_v_p.entity_id",
                ['parent_value'=>'c_p_e_v.value']
            );
    }

    
    
}
