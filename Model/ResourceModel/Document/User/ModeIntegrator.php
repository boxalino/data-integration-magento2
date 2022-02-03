<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\User;

use Boxalino\DataIntegration\Model\ResourceModel\BaseResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
use Boxalino\DataIntegration\Model\ResourceModel\Document\ModeIntegratorConditionalsTrait;
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

    use EntityResourceTrait;
    use ModeIntegratorConditionalsTrait;

    /**
     * On a daily basis, the users can be exported for the past week
     * OR since last update
     *
     * If the integration for delta is supported via MVIEW - it will also apply a filter by IDs
     *
     * @return string
     */
    public function getDeltaDateConditional() : string
    {
        return $this->getResourceDateConditional(
            ["c_e.updated_at", "c_e.created_at"],
            $this->dateConditional,
            true
        );
    }

    /**
     * Adding the instant filter condition on the main query
     * (as a general rule - the filter is by IDs provided from the MVIEW)
     *
     * @param Select $query
     * @param string $field
     * @return Select
     */
    public function addInstantCondition(Select $query, string $field = "c_e.entity_id") : Select
    {
        return $this->addResourceIdsConditional($query, "c_e.entity_id", $this->idsConditional);
    }

    /**
     * For the delta updates that use the MVIEW
     *
     * @param Select $query
     * @return Select
     */
    public function addDeltaIdsConditions(Select $query) : Select
    {
        return $this->addResourceIdsConditional($query, "c_e.entity_id", $this->idsConditional);
    }


}
