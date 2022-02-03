<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Order;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
use Boxalino\DataIntegration\Model\ResourceModel\Document\ModeIntegratorConditionalsTrait;
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

    use EntityResourceTrait;
    use ModeIntegratorConditionalsTrait;

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
            ["s_o.updated_at", "s_o.created_at"],
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
        return $this->addResourceIdsConditional($query, "s_o.entity_id", $this->idsConditional);
    }

    /**
     * For the delta updates that use the MVIEW
     *
     * @param Select $query
     * @return Select
     */
    public function addDeltaIdsConditional(Select $query) : Select
    {
        return $this->addResourceIdsConditional($query, "s_o.entity_id", $this->idsConditional);
    }


}
