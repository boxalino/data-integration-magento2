<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\User;

use Boxalino\DataIntegration\Model\ResourceModel\BaseResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
use Boxalino\DataIntegration\Model\ResourceModel\Document\GenericEntityResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\ModeIntegratorConditionalsTrait;
use Boxalino\DataIntegration\Model\ResourceModel\EavAttributeResourceTrait;
use Magento\Framework\DB\Select;

/**
 * Class ModeIntegrator
 * Assists different Data Sync Modes Integration (ex: delta, full, instant, paginated, etc)
 * in handling rules on the db queries
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\User
 */
abstract class ModeIntegrator extends DiSchemaDataProviderResource
{

    use GenericEntityResourceTrait;
    use EavAttributeResourceTrait;

    /**
     * @param array $storeIds
     * @return Select
     */
    public function getResourceByWebsiteIdWithChunkSelect(string $websiteId) : Select
    {
        $select = $this->getResourceByStoreIdsWebsiteIdSelect([], $websiteId);
        if($this->useDeltaIdsConditionals)
        {
            return $select;
        }

        /** @heldchen fix: must use ASC as in production systems */
        $select->where("{$this->getIdPrimaryKeyField()} > ?", $this->getChunk())
            ->order("{$this->getIdPrimaryKeyField()} ASC")
            ->limit((int)$this->getBatch());

        return $select;
    }

    /**
     * @param array $storeIds
     * @param string $websiteId
     * @return Select
     */
    public function getMainSelectByStoreIdsWebsiteId(array $storeIds, string $websiteId) : Select
    {
        return $this->adapter->select()
            ->from(
                ['c_e' => $this->adapter->getTableName('customer_entity')],
                ["*"]
            )
            ->where("c_e.website_id IN (?) " , $websiteId);
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
        $mainEntitySelect = $this->getResourceByWebsiteIdWithChunkSelect($websiteId);
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
    
    public function getIdPrimaryKeyField() : string
    {
        return 'c_e.entity_id';
    }

    /**
     * @return string
     */
    public function getCreatedAtField(): string
    {
        return "c_e.created_at";
    }

    /**
     * @return string
     */
    public function getUpdatedAtField(): string
    {
        return "c_e.updated_at";
    }


}
