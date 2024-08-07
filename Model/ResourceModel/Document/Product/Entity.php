<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Magento\Framework\DB\Select;

/**
 * Class Entity
 * Access the product_groups & sku information from the product table
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Entity extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllByFieldsWebsite(array $fields, string $websiteId)
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $relationParentTypeSelect = $this->getProductRelationEntityTypeSelect();
        $select = $this->adapter->select()
            ->from(
                ['c_p_e' => $this->adapter->getTableName('catalog_product_entity')],
                $fields
            )
            ->joinLeft(
                ['e_a_s' => $this->adapter->getTableName('eav_attribute_set')],
                "e_a_s.attribute_set_id = c_p_e.attribute_set_id",
                []
            )
            ->joinLeft(
                ['c_p_r' => new \Zend_Db_Expr("( ". $relationParentTypeSelect->__toString() . ' )')],
                "c_p_r.child_id = c_p_e.entity_id",
                []
            )
            ->joinLeft(
                ['c_p_r_p' => new \Zend_Db_Expr("( ". $relationParentTypeSelect->__toString() . ' )')],
                "c_p_r_p.parent_id = c_p_e.entity_id",
                []
            )
            ->joinLeft(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                "c_p_e_s.entity_id=c_p_e.entity_id",
                []
            )
            ->where("c_p_e_s.entity_id IS NOT NULL")
            ->group("c_p_e.entity_id");

        return $this->adapter->fetchAll($select);
    }


}
