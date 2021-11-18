<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Order;

use Magento\Framework\DB\Select;

/**
 * Class Entity
 * Access the main information about the orders
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Order
 */
class Entity extends ModeIntegrator
{

    /**
     * The following information is required for the entity load:
     * - default sales_order content
     * - sales_order_tax
     * - sales_order_payment
     *
     * @param array $fields
     * @param array $storeIds
     * @return array
     */
    public function getFetchAllByFieldsStoreIds(array $fields, array $storeIds)
    {
        $mainEntitySelect = $this->getEntityByStoreIdsSelect($storeIds);
        $taxSelect = $this->appendPrefixToColumnsGroupBySelect("sales_order_tax", "s_o_t", "order_id");
        $paymentSelect = $this->appendPrefixToColumnsGroupBySelect("sales_order_payment", "s_o_p", "parent_id");
        $shipmentTrackSelect = $this->appendPrefixToColumnsGroupBySelect("sales_shipment_track", "s_s_t", "parent_id");
        $select = $this->adapter->select()
            ->from(
                ['s_o' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['s_o_t' => new \Zend_Db_Expr("( ". $taxSelect->__toString() . ' )')],
                "s_o_t.s_o_t_order_id = s_o.entity_id",
                []
            )
            ->joinLeft(
                ['s_o_p' => new \Zend_Db_Expr("( ". $paymentSelect->__toString() . ' )')],
                "s_o_p.s_o_p_parent_id = s_o.entity_id",
                []
            )
            ->joinLeft(
                ['s_s_t' => new \Zend_Db_Expr("( ". $shipmentTrackSelect->__toString() . ' )')],
                "s_s_t.s_s_t_order_id = s_o.entity_id",
                []
            )
            ->group("s_o.entity_id");

        return $this->adapter->fetchAll($select);
    }




}
