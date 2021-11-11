<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;


use Magento\Framework\DB\Select;

/**
 * Class ProductRelation
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class ProductRelation extends ModeIntegrator
{

    /**
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllSuperLinkByWebsiteId(string $websiteId) : array
    {
        $superLinkSelect = $this->_getSuperLinkSelect();
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $select = $this->adapter->select()
            ->from(
                ["c_p_e_a_s" => new \Zend_Db_Expr("( " . $superLinkSelect->__toString() . " )")],
                ["*"]
            )
            ->joinLeft(
                ["c_p_e_s" => new \Zend_Db_Expr("( " . $mainEntitySelect->__toString() . " )")],
                "c_p_e_s.entity_id = c_p_e_a_s.entity_id",
                []
            )
            ->where("c_p_e_s.entity_id IS NOT NULL");

        return $this->adapter->fetchAll($select);
    }

    /**
     * @return Select
     */
    protected function _getSuperLinkSelect() : Select
    {
        $linkTypeSelect = $this->_getSuperLinkPropertyNameSelect();
        return $this->adapter->select()
            ->from(
                ["c_p_s_l" => $this->adapter->getTableName('catalog_product_super_link')],
                ['entity_id' => 'product_id', 'product_group' => 'parent_id', new \Zend_Db_Expr("'super' AS type")]
            )
            ->joinLeft(
                ["c_p_s_l_a" => new \Zend_Db_Expr("( " . $linkTypeSelect->__toString() . " )")],
                "c_p_s_l_a.entity_id = c_p_s_l.parent_id",
                ["name"]
            );
    }

    /**
     * @return Select
     */
    protected function _getSuperLinkPropertyNameSelect() : Select
    {
        return $this->adapter->select()
            ->from(
                ["c_p_s_a" => $this->adapter->getTableName('catalog_product_super_attribute')],
                ['entity_id' => 'product_id', new \Zend_Db_Expr("GROUP_CONCAT(e_a.attribute_code SEPARATOR ',') AS name")]
            )
            ->joinLeft(
                ["e_a" => $this->adapter->getTableName('eav_attribute')],
                "c_p_s_a.attribute_id = e_a.attribute_id",
                []
            )->group("product_id");
    }

    /**
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllLinkByWebsiteId(string $websiteId) : array
    {
        $linkSelect = $this->_getLinkSelect();
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $select = $this->adapter->select()
            ->from(
                ["c_p_e_a_s" => new \Zend_Db_Expr("( " . $linkSelect->__toString() . " )")],
                ["*"]
            )
            ->joinLeft(
                ["c_p_e_s" => new \Zend_Db_Expr("( " . $mainEntitySelect->__toString() . " )")],
                "c_p_e_s.entity_id = c_p_e_a_s.entity_id",
                []
            )
            ->where("c_p_e_s.entity_id IS NOT NULL");

        return $this->adapter->fetchAll($select);
    }

    /**
     * @return array
     */
    public function _getLinkSelect() : Select
    {
        return $this->adapter->select()
            ->from(
                ['c_p_l'=> $this->adapter->getTableName('catalog_product_link')],
                ['entity_id' => 'product_id', 'sku' => 'linked_product_id', "type"=> 'lt.code']
            )
            ->joinLeft(
                ['c_p_l_t' => $this->adapter->getTableName('catalog_product_link_type')],
                'c_p_l.link_type_id = c_p_l_t.link_type_id', []
            )
            ->where('lt.link_type_id = pl.link_type_id');
    }


}
