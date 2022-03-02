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
     * @return Select
     */
    public function addDeltaDateConditional(Select $query) : Select
    {
        return $this->adapter->select()
            ->distinct(true)
            ->from(
                ['c_e' => new \Zend_Db_Expr("( " . $query->__toString() . ' )')],
                ["e_e.*"]
            )
            ->joinLeft(
                ['e_e' => $this->adapter->getTableName('catalog_product_entity')],
                "e_e.entity_id = c_e.entity_id",
                []
            )
            ->joinLeft(
                ['e_p' => $this->adapter->getTableName('catalog_product_entity')],
                "e_p.entity_id = c_e.as_parent",
                []
            )
            ->joinLeft(
                ['e_c' => $this->adapter->getTableName('catalog_product_entity')],
                "e_c.entity_id = c_e.as_child",
                []
            )
            ->where(
                implode(" OR ", [
                        $this->getDeltaDateConditional(["e_e.updated_at", "e_e.created_at"]),
                        $this->getDeltaDateConditional(["e_p.updated_at", "e_p.created_at"]),
                        $this->getDeltaDateConditional(["e_c.updated_at", "e_c.created_at"])
                    ]
                )
            );
    }

    /**
     * @return string
     */
    public function getDeltaDateConditional(array $conditionalFields = ["e.updated_at", "e.created_at"]) : string
    {
        return $this->getResourceDateConditional(
            $conditionalFields,
            $this->dateConditional
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
        return $this->addDeltaIdsConditional($query);
    }

    /**
     * For the delta updates that use the MVIEW
     * @updated Include the parent_ids and all other children in the synced IDs
     *
     * @param Select $query
     * @return Select
     */
    public function addDeltaIdsConditional(Select $query) : Select
    {
        if(empty($this->idsConditional))
        {
            return $query;
        }

        $select = $this->adapter->select()
            ->distinct(true)
            ->from(
                ['e' => $this->adapter->getTableName('catalog_product_entity')],
                ["*"]
            )
            ->joinLeft(
                ['c_e' => new \Zend_Db_Expr("( ". $query->__toString() . ' )')],
                "e.entity_id = c_e.entity_id",
                []
            )
            ->where("c_e.entity_id IN (?) OR c_e.as_parent IN (?) OR c_e.as_child IN (?)", $this->idsConditional);

        return $select;
    }


}
