<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Order;

use Boxalino\DataIntegration\Model\ResourceModel\BaseResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
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
    use BaseResourceTrait;

    /**
     * On a daily basis, the orders can be exported for the past week
     * OR since last update
     *
     * If the integration for delta is supported via MVIEW - it will also apply a filter by IDs
     *
     * @param string $dateCriteria
     * @return string
     */
    public function getDeltaDateConditional(string $dateCriteria, array $conditionalFields = ["s_o.updated_at", "s_o.created_at"]) : string
    {
        $conditions = [];
        foreach($conditionalFields as $field)
        {
            $conditions[] = " STR_TO_DATE($field, '%Y-%m-%d %H:%i') > '$dateCriteria' ";
        }

        return implode("OR", $conditions);
    }

    /**
     * Adding the instant filter condition on the main query
     * (as a general rule - the filter is by IDs provided from the MVIEW)
     *
     * @param Select $query
     * @param array $ids
     * @param string $field
     * @return Select
     */
    protected function addInstantCondition(Select $query, array $ids, string $field = "s_o.entity_id") : Select
    {
        $query->andWhere("$field IN (?)", $ids);
        return $query;
    }

}
