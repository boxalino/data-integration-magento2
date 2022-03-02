<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document;

use Magento\Framework\DB\Select;

/**
 * Interface ModeIntegratorConditionalsTrait
 */
trait ModeIntegratorConditionalsTrait
{

    /**
     * Adding the instant filter condition on the main query
     * (as a general rule - the filter is by IDs provided from the MVIEW)
     *
     * @param Select $query
     * @param string $field
     * @param array $ids
     * @return Select
     */
    public function addResourceIdsConditional(Select $query, string $field, array $ids = []) : Select
    {
        if(empty($ids))
        {
            return $query;
        }

        $query->where("$field IN (?)", $ids);
        return $query;
    }

    /**
     * On a daily basis, the content can be exported for the past week
     * OR since last update
     *
     * If the integration for delta is supported via MVIEW - it will also apply a filter by IDs
     *
     * @param array $conditionalFields
     * @param string $dateConditional
     * @return string
     */
    public function getResourceDateConditional(array $conditionalFields, string $dateConditional, bool $addAndConditional = false) : string
    {
        $orConditions = $this->_getDateConditional($conditionalFields, ">", "'$dateConditional'");
        if($addAndConditional)
        {
            $andConditions = $this->_getDateConditional($conditionalFields, "<=", "STR_TO_DATE(DATE_SUB(NOW(), INTERVAL 5 MINUTE), '%Y-%m-%d %H:%i:%s')");
            return implode(" AND ",["(" . $orConditions . ")",  "(" . $andConditions . ")"]);
        }

        return $orConditions;
    }

    /**
     * @param array $conditionalFields
     * @param string $symbol
     * @param string $date
     * @param bool $and
     * @return string
     */
    protected function _getDateConditional(array $conditionalFields, string $symbol, string $date, bool $and = false) : string
    {
        $conditions = [];
        foreach($conditionalFields as $field)
        {
            $conditions[] = " STR_TO_DATE($field, '%Y-%m-%d %H:%i:%s') $symbol $date ";
        }

        if($and)
        {
            return implode(" AND", $conditions);
        }

        return implode(" OR", $conditions);
    }


}
