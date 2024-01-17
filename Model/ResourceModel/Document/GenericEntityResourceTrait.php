<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document;

use Magento\Framework\DB\Select;

/**
 * Helper trait for manipulating date/id conditionals on the main select for the resource
 */
trait GenericEntityResourceTrait
{
    use ModeIntegratorConditionalsTrait;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $adapter;
    /**
     * @var string
     */
    protected $idPrimaryKeyField = 'entity_id';

    /**
     * @var string
     */
    protected $createdAtField = 'created_at';

    /**
     * @var string
     */
    protected $updatedAtField = 'updated_at';

    /**
     * @param array $storeIds
     * @param string $websiteId
     * @return Select
     */
    abstract function getMainSelectByStoreIdsWebsiteId(array $storeIds, string $websiteId) : Select;

    /**
     * @param array $storeIds
     * @param string $websiteId
     * @return Select
     */
    public function getResourceByStoreIdsWebsiteIdSelect(array $storeIds, string $websiteId): Select
    {
        $select = $this->getMainSelectByStoreIdsWebsiteId($storeIds, $websiteId);

        if($this->useDeltaIdsConditionals)
        {
            return $this->addDeltaIdsConditional($select);
        }

        if($this->delta)
        {
            $select->where($this->getDeltaDateConditional());
        }

        if($this->instant)
        {
            $select = $this->addInstantConditional($select);
        }

        return $select;
    }

    /**
     * On a daily basis, the orders can be exported for the past week
     * OR since last update
     *
     * If the integration for delta is supported via MVIEW - it will also apply a filter by IDs
     *
     * @return string
     */
    public function getDeltaDateConditional() : string
    {
        return $this->getResourceDateConditional(
            array_filter([$this->getCreatedAtField(), $this->getUpdatedAtField()]),
            $this->dateConditional,
            true
        );
    }

    /**
     * Adding the instant filter condition on the main query
     * (as a general rule - the filter is by IDs provided from the MVIEW)
     *
     * @param Select $query
     * @return Select
     */
    public function addInstantConditional(Select $query) : Select
    {
        return $this->addResourceIdsConditional($query, $this->getIdPrimaryKeyField(), $this->idsConditional);
    }

    /**
     * For the delta updates that use the MVIEW
     *
     * @param Select $query
     * @return Select
     */
    public function addDeltaIdsConditional(Select $query) : Select
    {
        return $this->addResourceIdsConditional($query, $this->getIdPrimaryKeyField(), $this->idsConditional);
    }

    /**
     * @return string
     */
    public function getIdPrimaryKeyField(): string
    {
        return $this->idPrimaryKeyField;
    }

    /**
     * @return string
     */
    public function getCreatedAtField(): string
    {
        return $this->createdAtField;
    }

    /**
     * @return string
     */
    public function getUpdatedAtField(): string
    {
        return $this->updatedAtField;
    }


}
