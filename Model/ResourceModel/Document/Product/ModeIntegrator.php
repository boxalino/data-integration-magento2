<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
use Boxalino\DataIntegration\Model\ResourceModel\Document\ModeIntegratorConditionalsTrait;
use Magento\Framework\DB\Select;

/**
 * Class ModeIntegrator
 * Assists different Data Sync Modes Integration (ex: delta, full, instant, paginated, etc)
 * in handling rules on the db queries
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
abstract class ModeIntegrator extends DiSchemaDataProviderResource
{

    use EntityResourceTrait;
    use ModeIntegratorConditionalsTrait;

    /**
     * As a daily basis, the products can be exported for the past hour only
     * OR since last update
     *
     * If the integration for delta is supported via MVIEW - it will also apply a filter by IDs
     *
     * @return string
     */
    public function getDeltaDateConditional(array $conditionalFields = ["e.updated_at", "e.created_at"]) : string
    {
        return $this->getResourceDateConditional(
            ["e.updated_at", "e.created_at"],
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
    public function addInstantConditional(Select $query, string $field = "e.entity_id") : Select
    {
        return $this->addResourceIdsConditional($query, "e.entity_id", $this->idsConditional);
    }

    /**
     * For the delta updates that use the MVIEW
     *
     * @param Select $query
     * @return Select
     */
    public function addDeltaIdsConditional(Select $query) : Select
    {
        return $this->addResourceIdsConditional($query, "e.entity_id", $this->idsConditional);
    }


}
