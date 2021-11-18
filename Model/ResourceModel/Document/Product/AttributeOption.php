<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeOptionResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavProductResourceTrait;

/**
 * Class AttributeOption
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class AttributeOption extends ModeIntegrator
{

    use EavProductResourceTrait;
    use EavAttributeResourceTrait;
    use EavAttributeOptionResourceTrait;

    /**
     * For attribute-options that have content with same label on all storeviews (ex: brand names)
     *
     * @param array $fields
     * @param string $websiteId
     * @param int $attributeId
     * @return array
     */
    public function getFetchAllByWebsiteAttributeId(array $fields, string $websiteId, int $attributeId) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $attributeOptionAdminValueSelect = $this->getAttributeOptionCodeByAttributeIdSelect($attributeId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['c_p_e_i' => $this->adapter->getTableName('catalog_product_entity_int')],
                "c_p_e_s.entity_id = c_p_e_i.entity_id AND c_p_e_i.attribute_id= $attributeId",
                []
            )
            ->joinLeft(
                ['c_p_e_a_s' => new \Zend_Db_Expr("( ". $attributeOptionAdminValueSelect->__toString() . ' )')],
                "c_p_e_a_s.option_id = c_p_e_i.value",
                []
            );

        return $this->adapter->fetchAll($select);
    }

    /**
     * For every fetched property - adds translation and connection to product
     * 
     * @param array $fields
     * @param string $websiteId
     * @param int $storeId
     * @param int $attributeId
     * @param string type
     * @return array
     */
    public function getFetchAllForLocalizedAttributeByStoreId(array $fields, string $websiteId, int $storeId, int $attributeId, string $type) : array
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
            ->where('c_p_e_s.entity_id IS NOT NULL')
            ->where('c_p_e_a_s.value IS NOT NULL');

        return $this->adapter->fetchAll($select);
    }


}
