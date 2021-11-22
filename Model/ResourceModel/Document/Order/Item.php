<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Order;

use Magento\Framework\DB\Select;

/**
 * Class Item
 * Access the data about order items
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Order
 */
class Item extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param array $storeIds
     * @return array
     */
    public function getFetchAllByFieldsStoreIds(array $fields, array $storeIds) : array
    {
        $rows = [];
        foreach($this->getSelectsCollectionForItemFetchAll($fields, $storeIds) as $select)
        {
            $rows = array_merge($rows, $this->adapter->fetchAll($select));
        }

        return $rows;
    }

    /**
     * @param array $fields
     * @param array $storeIds
     * @return array
     */
    protected function getSelectsCollectionForItemFetchAll(array $fields, array $storeIds) : array
    {
        return [
            $this->getSalesOrderItemSelectByType($fields, $storeIds, "simple"),
            $this->getSalesOrderItemSelectByType($fields, $storeIds, \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE),
            $this->getConfigurableSalesOrderItemSelect($fields, $storeIds),
            $this->getGroupedConfigurableSalesOrderItemSelect($fields, $storeIds)
        ];
    }

    /**
     * Access items for the bought configurable products with link to the variant ID
     * - must not be part of grouped products
     * @param array $fields
     * @param array $storeIds
     * @return Select
     */
    protected function getGroupedConfigurableSalesOrderItemSelect(array $fields, array $storeIds) : Select
    {
        $fieldsForGrouped = [
            "product_id", "product_type", "sku", "name",
            "JSON_EXTRACT(s_o_i.product_options, '$.super_product_config.product_id') AS main_product_id"
        ];
        $fieldsForConfigurable = array_diff($this->getColumnsByTableName("sales_order_item"), $fieldsForGrouped);

        $mainEntitySelect = $this->getEntityByStoreIdsSelect($storeIds);
        $groupedProductType = \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE;
        $configurableProductType = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
        $select = $this->adapter->select()
            ->from(
                ['s_o_i' => $this->adapter->getTableName("sales_order_item")],
                $fieldsForGrouped
            )
            ->joinLeft(
                ['s_o' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                "s_o_i.order_id = s_o.entity_id",
                [$fields[0]]
            )
            ->joinLeft(
                ['s_o_i_configurable' => $this->adapter->getTableName("sales_order_item")],
                "s_o_i.parent_item_id = s_o_i_configurable.item_id AND s_o_i.product_type='$configurableProductType'",
                $fieldsForConfigurable
            )
            ->where("s_o.entity_id IS NOT NULL")
            ->where("s_o_i.product_type='$groupedProductType'")
            ->where("s_o_i.parent_item_id IS NOT NULL");

        return $select;
    }

    /**
     * Access items for the bought configurable products with link to the variant ID
     * - must not be part of grouped products
     * @param array $fields
     * @param array $storeIds
     * @return Select
     */
    protected function getConfigurableSalesOrderItemSelect(array $fields, array $storeIds) : Select
    {
        $mainEntitySelect = $this->getEntityByStoreIdsSelect($storeIds);
        $groupedProductType = \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE;
        $configurableProductType = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
        $select = $this->adapter->select()
            ->from(
                ['s_o_i' => $this->adapter->getTableName("sales_order_item")],
                $fields
            )
            ->joinLeft(
                ['s_o' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                "s_o_i.order_id = s_o.entity_id",
                []
            )
            ->joinLeft(
                ['s_o_i_grouped' => $this->adapter->getTableName("sales_order_item")],
                "s_o_i_grouped.product_type='$groupedProductType' AND s_o_i_grouped.parent_item_id=s_o_i.item_id",
                []
            )
            ->joinLeft(
                ['s_o_i_configurable' => $this->adapter->getTableName("sales_order_item")],
                "s_o_i.item_id = s_o_i_configurable.parent_item_id AND s_o_i.product_type='$configurableProductType'",
                ["main_product_id"=>"s_o_i_configurable.product_id"]
            )
            ->where("s_o.entity_id IS NOT NULL")
            ->where("s_o_i.product_type='$configurableProductType'")
            ->where("s_o_i.parent_item_id IS NULL")
            ->where("s_o_i_grouped.item_id IS NULL");

        return $select;
    }

    /**
     * @param array $fields
     * @param array $storeIds
     * @param string $productType
     * @return Select
     */
    protected function getSalesOrderItemSelectByType(array $fields, array $storeIds, string $productType) : Select
    {
        $fields = array_merge(["main_product_id" => "product_id", "product_id"=>"product_id"], $fields);
        $mainEntitySelect = $this->getEntityByStoreIdsSelect($storeIds);
        $select = $this->adapter->select()
            ->from(
                ['s_o_i' => $this->adapter->getTableName("sales_order_item")],
                $fields
            )
            ->joinLeft(
                ['s_o' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                "s_o_i.order_id = s_o.entity_id",
                []
            )
            ->where("s_o.entity_id IS NOT NULL")
            ->where("s_o_i.product_type='$productType'")
            ->where("s_o_i.parent_item_id IS NULL");

        return $select;
    }


}
