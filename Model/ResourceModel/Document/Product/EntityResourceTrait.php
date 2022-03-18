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
        $select = $this->_getEntityByWebsiteIdSelect($websiteId);
        if($this->useDeltaIdsConditionals)
        {
            return $this->addDeltaIdsConditional($select);
        }

        if($this->instant)
        {
            return $this->addInstantConditional($select);
        }

        if($this->delta)
        {
            $select = $this->_getEntityIdsWithRelationsBySelect($select);
            return $this->addDeltaDateConditional($select);
        }

        return $select;
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
     * MVIEW / DELTA DRIVEN EXPORTS
     *
     * @return Select
     */
    protected function _getEntityIdsWithRelationsBySelect(Select $mainEntitySelect) : Select
    {
        $relationParentTypeSelect = $this->getRelationEntityTypeSelect();
        $affectedGroupSelect = $this->getAffectedParentGroupSelect();
        return $this->adapter->select()
            ->from(
                ['c_p_e' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                ['c_p_e.entity_id', 'as_parent'=>'c_p_r.parent_id', 'as_child'=>'c_p_r_p.child_id', 'c_p_r_r.child_id', 'c_p_r_r.parent_id']
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
                ['c_p_r_r' => new \Zend_Db_Expr("( ". $affectedGroupSelect->__toString() . ' )')],
                "c_p_r_r.affected = c_p_e.entity_id",
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

    /**
     * Inner join to get a list of any possible affected items when a detached id is updated
     *
     * @return Select
     */
    protected function getAffectedParentGroupSelect() : Select
    {
        return $this->adapter->select()
            ->from(
                ['c_p_r_affected' => $this->adapter->getTableName('catalog_product_relation')],
                ["c_p_r_affected.parent_id", "affected"=>'c_p_r_r_affected.child_id', 'c_p_r_affected.child_id']
            )
            ->joinInner(
                ['c_p_r_r_affected' => $this->adapter->getTableName('catalog_product_relation')],
                "c_p_r_affected.parent_id = c_p_r_r_affected.parent_id",
                []
            );
    }


}
