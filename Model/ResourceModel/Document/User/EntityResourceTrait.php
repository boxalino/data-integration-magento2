<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\User;

use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;
use Magento\Framework\DB\Select;

/**
 * Helper trait for accessing eav content
 * (joins, selects, etc)
 */
trait EntityResourceTrait
{
    use EavAttributeResourceTrait;

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
     * @param array $storeIds
     * @return Select
     */
    public function getEntityByWebsiteIdSelect(string $websiteId): Select
    {
        $select = $this->adapter->select()
            ->from(
                ['c_e' => $this->adapter->getTableName('customer_entity')],
                ["*"]
            )
            ->where("c_e.website_id IN (?) " , $websiteId);

        if($this->useDeltaIdsConditionals)
        {
            return $this->addDeltaIdsConditions($select);
        }

        if($this->delta)
        {
            $select->where($this->getDeltaDateConditional());
        }

        if($this->instant)
        {
            $select = $this->addInstantCondition($select);
        }

        /** using a SEEK strategy for batching data */
        $select->where("c_e.entity_id > ?", $this->getChunk())
            ->order("c_e.entity_id ASC")
            ->limit((int)$this->getBatch());

        return $select;
    }

    /**
     * @return array
     */
    public function getFetchPairsAttributes(int $entityTypeId = \Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER) : array
    {
        $select = $this->adapter->select()
            ->from(
                ['a_t' => $this->adapter->getTableName('eav_attribute')],
                ['attribute_code', 'backend_type']
            )
            ->where('a_t.entity_type_id = ?', $entityTypeId)
            ->where('a_t.backend_type !="static"');

        return $this->adapter->fetchPairs($select);
    }

    /**
     * @param string $attributeCode
     * @param int $entityTypeId
     * @param string $type
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllAttributeContent(string $attributeCode, int $entityTypeId, string $type, string $websiteId) : array
    {
        $attributeId = $this->getAttributeIdByAttributeCodeAndEntityTypeId($attributeCode, $entityTypeId);
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $select = $this->adapter->select()
            ->from(
                ['c_e_a' => $this->adapter->getTableName("customer_entity_$type")],
                ['value_id','entity_id', 'value']
            )
            ->joinLeft(
                ['c_e' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                "c_e_a.entity_id = c_e.entity_id",
                []
            )
            ->where('c_e_a.attribute_id = ?', $attributeId)
            ->where('c_e.entity_id IS NOT NULL');

        return $this->adapter->fetchAll($select);
    }

    
}
