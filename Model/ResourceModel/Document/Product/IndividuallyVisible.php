<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

/**
 * Class IndividuallyVisible
 * Exports the detail about the SKU being individually visible
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class IndividuallyVisible extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param string $websiteId
     * @param int $attributeId
     * @param int $storeId
     * @param string $table
     * @return array
     */
    public function getFetchPairsByFieldsWebsiteStoreAttrIdTable(array $fields, string $websiteId, int $attributeId, int $storeId, string $table) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $propertySelect = $this->getEavJoinAttributeSQLByStoreAttrIdTable($attributeId, $storeId, $table);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['c_p_e_a_s' => new \Zend_Db_Expr("( ". $propertySelect->__toString() . ' )')],
                'c_p_e_a_s.entity_id = c_p_e_s.entity_id',
                []
            );

        return $this->adapter->fetchPairs($select);
    }


}
