<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Magento\Framework\DB\Select;

/**
 * Helper trait for accessing eav content
 * (joins, selects, etc)
 */
trait EntityResourceTrait
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @param string $websiteId
     * @return array
     */
    public function getEntityByWebsiteId(string $websiteId): array
    {
        return $this->adapter->fetchAll($this->getEntityByWebsiteIdSelect($websiteId));
    }

    /**
     * @param string $websiteId
     * @return Select
     */
    public function getEntityByWebsiteIdSelect(string $websiteId): Select
    {
        $select = $this->_getEntityIdsWithRelationsByWebsiteIdSelect($websiteId);
        if($this->useDeltaIdsConditionals)
        {
            return $this->addDeltaIdsConditional($select);
        }

        if($this->delta)
        {
            return $this->addDeltaDateConditional($select);
        }

        if($this->instant)
        {
            return $this->addInstantConditional($select);
        }

        return $this->_getEntityByWebsiteIdSelect($websiteId);
    }

    /**
     * Generic ENTITY SELECT for FULL export
     *
     * @param string $websiteId
     * @return Select
     */
    protected function _getEntityByWebsiteIdSelect(string $websiteId): Select
    {
        return $this->adapter->select()
            ->from(
                ['e' => $this->adapter->getTableName('catalog_product_entity')],
                ["*"]
            )
            ->joinLeft(
                ['c_p_w' => $this->adapter->getTableName('catalog_product_website')],
                'e.entity_id = c_p_w.product_id AND c_p_w.website_id = ' . $websiteId,
                []
            )
            ->where("c_p_w.website_id= ? " , $websiteId);
    }

    /**
     * USED FOR MVIEW / DELTA DRIVEN EXPORTS
     *
     * @param string $websiteId
     * @return Select
     */
    protected function _getEntityIdsWithRelationsByWebsiteIdSelect(string $websiteId) : Select
    {
        $mainEntitySelect = $this->_getEntityByWebsiteIdSelect($websiteId);
        $relationParentTypeSelect = $this->getRelationEntityTypeSelect();
        return $this->adapter->select()
            ->from(
                ['c_p_e' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                ['c_p_e.entity_id', 'as_parent'=>'c_p_r.parent_id', 'as_child'=>'c_p_r_p.child_id']
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
            ->where("c_p_e.entity_id IS NOT NULL");
    }

    /**
     * Filter out parent_ids which no longer exist in the DB
     *
     * @return Select
     */
    protected function getRelationEntityTypeSelect() : Select
    {
        return $this->adapter->select()
            ->from(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                ["parent_id", "child_id"]
            )
            ->joinLeft(
                ['c_p_e' => $this->adapter->getTableName('catalog_product_entity')],
                "c_p_r.parent_id = c_p_e.entity_id",
                ["parent_type_id"=>"type_id"]
            )
            ->where("c_p_e.entity_id IS NOT NULL");
    }


}
