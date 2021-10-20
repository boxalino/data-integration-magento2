<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

/**
 * Class AttributeGlobal
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class AttributeGlobal extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param string $websiteId
     * @param array $storeIds
     * @param int $attributeId
     * @param string $type
     * @return array
     */
    public function getValuesForGlobalAttribute(array $fields, string $websiteId, array $storeIds, int $attributeId, string $type) : array
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

        return $this->adapter->fetchAll($select);
    }

}
