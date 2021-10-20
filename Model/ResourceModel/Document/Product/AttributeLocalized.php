<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;


/**
 * Class AttributeLocalized
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class AttributeLocalized extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param string $websiteId
     * @param int $storeId
     * @param int $attributeId
     * @param string $type
     * @return array
     */
    public function getValuesForLocalizedAttributeByStoreId(array $fields, string $websiteId, int $storeId, int $attributeId, string $type) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $eavPropertySelect = $this->getEavJoinAttributeSQLByStoreAttrIdTable($attributeId, $storeId, "catalog_product_entity_$type");
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
            ->where('c_p_e_s.entity_id IS NOT NULL');


        return $this->adapter->fetchPairs($select);
    }
}
