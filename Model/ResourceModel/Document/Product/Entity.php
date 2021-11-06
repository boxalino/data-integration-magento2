<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

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
        $select = $this->adapter->select()
            ->from(
                ['c_p_e' => $this->adapter->getTableName('catalog_product_entity')],
                $fields
            )
            ->joinLeft(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                "c_p_r.child_id = c_p_e.entity_id",
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
