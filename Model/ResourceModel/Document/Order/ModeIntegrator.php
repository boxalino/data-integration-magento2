<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Order;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
use Boxalino\DataIntegration\Model\ResourceModel\Document\GenericEntityResourceTrait;
use Magento\Framework\DB\Select;

/**
 * Class ModeIntegrator
 * Assists different Data Sync Modes Integration (ex: delta, full, instant, paginated, etc)
 * in handling rules on the db queries
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Order
 */
abstract class ModeIntegrator extends DiSchemaDataProviderResource
{

    use GenericEntityResourceTrait;

    /**
     * @var string 
     */
    protected $defaultWebsiteId = "0";

    /**
     * @param array $storeIds
     * @return array
     */
    public function getEntityByStoreIds(array $storeIds): array
    {
        return $this->adapter->fetchAll($this->getResourceByStoreIdsWithChunkSelect($storeIds, $this->defaultWebsiteId));
    }

    /**
     * @param array $storeIds
     * @return Select
     */
    public function getResourceByStoreIdsWithChunkSelect(array $storeIds) : Select
    {
        $select = $this->getResourceByStoreIdsWebsiteIdSelect($storeIds, $this->defaultWebsiteId);
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
                ['s_o' => $this->adapter->getTableName('sales_order')],
                ["*"]
            )
            ->where("s_o.store_id IN (?) OR s_o.store_id = 0" , $storeIds);
    }

    public function getIdPrimaryKeyField() : string
    {
        return 's_o.entity_id';
    }

    /**
     * @return string
     */
    public function getCreatedAtField(): string
    {
        return "s_o.created_at";
    }

    /**
     * @return string
     */
    public function getUpdatedAtField(): string
    {
        return "s_o.updated_at";
    }
    

}
