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
        $select = $this->adapter->select()
            ->from(
                ['c_p_e' => new \Zend_Db_Expr("( ". $this->getEntityByWebsiteIdSelect($websiteId)->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['e_a_s' => $this->adapter->getTableName('eav_attribute_set')],
                "e_a_s.attribute_set_id = c_p_e.attribute_set_id",
                []
            )
            ->joinLeft(
                ['c_p_r' => new \Zend_Db_Expr("( ". $this->getProductRelationByFieldWebsiteSelect("parent_id", $websiteId)->__toString() . ' )')],
                "c_p_r.child_id = c_p_e.entity_id",
                []
            )
            ->joinLeft(
                ['c_p_r_p' => new \Zend_Db_Expr("( ". $this->getProductRelationByFieldWebsiteSelect("child_id", $websiteId)->__toString() . ' )')],
                "c_p_r_p.parent_id = c_p_e.entity_id",
                []
            )
            ->group("c_p_e.entity_id");

        return $this->adapter->fetchAll($select);
    }


}
