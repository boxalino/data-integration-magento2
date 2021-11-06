<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavProductResourceTrait;
use Magento\Framework\DB\Select;

/**
 * Class AttributeGlobal
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class AttributeGlobal extends ModeIntegrator
{

    use EavAttributeResourceTrait;
    use EavProductResourceTrait;

    /**
     * @param array $fields
     * @param string $websiteId
     * @param array $storeIds
     * @param int $attributeId
     * @param string $type
     * @return array
     */
    public function getFetchPairsForGlobalAttribute(array $fields, string $websiteId, array $storeIds, int $attributeId, string $type) : array
    {
        $select = $this->_getSqlForGlobalAttribute($fields, $websiteId, $storeIds, $attributeId, $type);
        return $this->adapter->fetchPairs($select);
    }

    /**
     * @param array $fields
     * @param string $websiteId
     * @param array $storeIds
     * @param int $attributeId
     * @param string $type
     * @return array
     */
    public function getSelectAllForGlobalAttribute(array $fields, string $websiteId, array $storeIds, int $attributeId, string $type) : array
    {
        $select = $this->_getSqlForGlobalAttribute($fields, $websiteId, $storeIds, $attributeId, $type);
        return $this->adapter->fetchAll($select);
    }

    /**
     * @param array $fields
     * @param string $websiteId
     * @param array $storeIds
     * @param int $attributeId
     * @param string $type
     * @return Select
     */
    protected function _getSqlForGlobalAttribute(array $fields, string $websiteId, array $storeIds, int $attributeId, string $type) : Select
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $eavPropertySelect = $this->getEavJoinAttributeSQLByStoresAttrIdTable($attributeId, $storeIds, "catalog_product_entity_$type");
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_a_s' => new \Zend_Db_Expr("( ". $eavPropertySelect->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                "c_p_e_s.entity_id = c_p_e_a_s.entity_id",
                []
            )
            ->where("c_p_e_s.entity_id IS NOT NULL");

        return $select;
    }


}
