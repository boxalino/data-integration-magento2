<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\DiSchemaDataProviderResource;
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

    /**
     * As a daily basis, the products can be exported for the past hour only
     * OR since last update
     *
     * If the integration for delta is supported via MVIEW - it will also apply a filter by IDs
     *
     * @param string $dateCriteria
     * @return string
     */
    public function getDeltaDateConditional(string $dateCriteria, array $conditionalFields = ["c_p_e.updated_at", "c_p_e.created_at"]) : string
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
    protected function addInstantCondition(Select $query, array $ids, string $field = "c_p_e.entity_id") : Select
    {
        $query->andWhere("$field IN (?)", $ids);
        return $query;
    }

    protected function _getBaseEntityQuery(?array $fields = [])
    {
        if(empty($fields))
        {
            $fields = ["c_p_e.entity_id",  "c_p_e.updated_at", "c_p_e.created_at"];
        }
        $query->select($fields)
            ->from("product")
            ->leftJoin('product', 'product_visibility', 'pv', 'product.id = pv.product_id AND pv.sales_channel_id = :channelId')
            ->andWhere('product.version_id = :live')
            ->andWhere("JSON_SEARCH(product.category_tree, 'one', :channelRootCategoryId) IS NOT NULL OR pv.product_id IS NOT NULL")
            ->orderBy("product.product_number", "DESC")
            ->addOrderBy("product.created_at", "DESC")
            ->setFirstResult($this->getFirstResultByBatch())
            ->setMaxResults($this->getSystemConfiguration()->getBatchSize());

        /** for delta requests */
        if($this->filterByCriteria())
        {
            return $this->addDeltaCondition($query);
        }

        /** for instant syncs */
        if($this->hasModeEnabled() & $this->filterByIds())
        {
            return $this->addInstantCondition($query);
        }

        return $query;
    }


}
