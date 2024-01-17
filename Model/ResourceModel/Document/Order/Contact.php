<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Order;

use Magento\Framework\DB\Select;

/**
 * Class Contact
 * Access the data about order addresses (billing and shipping)
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Order
 */
class Contact extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param array $storeIds
     * @return array
     */
    public function getFetchAllByFieldsStoreIds(array $fields, array $storeIds) : array
    {
        $mainEntitySelect = $this->getResourceByStoreIdsWithChunkSelect($storeIds);
        $customerSelect = $this->appendPrefixToColumnsGroupBySelect("customer_entity", "c_e", "entity_id");
        $select = $this->adapter->select()
            ->from(
                ['s_o_a' => $this->adapter->getTableName("sales_order_address")],
                $fields
            )
            ->joinLeft(
                ['s_o' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                "s_o_a.parent_id = s_o.entity_id",
                []
            )
            ->joinLeft(
                ['c_e' => new \Zend_Db_Expr("( ". $customerSelect->__toString() . ' )')],
                "s_o_a.email = c_e.c_e_email",
                []
            )
            ->joinLeft(
                ['c_g' => $this->adapter->getTableName("customer_group")],
                "c_g.customer_group_id = c_e.c_e_group_id",
                ["customer_group_code"]
            )
            ->where("s_o.entity_id IS NOT NULL");

        return $this->adapter->fetchAll($select);
    }



}
