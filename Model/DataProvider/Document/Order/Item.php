<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderItemPropertyInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Order\Item as DataProviderResourceModel;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocOrderHandlerInterface;

/**
 * Class Item
 * Handles the order content details (product, vouchers, etc)
 */
class Item extends ModeIntegrator
    implements DocOrderItemPropertyInterface
{

    /**
     * @param DataProviderResourceModel $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        return [
            new \Zend_Db_Expr("s_o.entity_id AS {$this->getDiIdField()}"),
            's_o_i.*'
        ];
    }

    /**
     * Creating a list of label-value elements to be added as string attributes
     *
     * @param array $item
     * @return array
     */
    public function getStringOptions(array $item) : array
    {
        $options = [];
        $options["id"] = [$item["product_id"]];
        $options["sku"] = [$item["sku"]];
        $options["name"] = [$item["name"]];
        $options["description"] = [$item["description"]];
        $options["free_shipping"] = [$item["free_shipping"]];
        $options["parent_item_id"] = [$item["parent_item_id"]];

        try{
            $itemOptions = json_decode($item["product_options"], true);
        } catch (\Throwable $exception) {
            $itemOptions = [];
        }
        if ($item["product_type"] === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
        {
            $options["name"] = [$itemOptions["simple_name"]];
            $options["linked_group_id"] = [$item["product_id"]];
            $options["id"] = [$item["main_product_id"]];
        }

        if($item["product_type"] === \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE)
        {
            $options["linked_group_id"] = [$item["main_product_id"]];
            if(isset($itemOptions["super_product_config"]))
            {
                $options["linked_group_id"] = [$itemOptions["super_product_config"]["product_id"]];
            }
        }

        if(isset($itemOptions["attributes_info"]))
        {
            foreach ($itemOptions["attributes_info"] as $option)
            {
                $options[$option['label']] = [$option['value']];
            }
        }

        return $options;
    }

    /**
     * Creating a list of label-value elements to be added as numeric attributes
     *
     * @param array $item
     * @return array
     */
    public function getNumericOptions(array $item) : array
    {
        $options = [];
        $options["qty_refunded"] = [$item["qty_refunded"]];
        $options["qty_shipped"] = [$item["qty_shipped"]];
        $options["cost"] = [$item["base_cost"]];
        $options["free_shipping"] = [$item["free_shipping"]];
        $options["discount_percent"] = [$item["discount_percent"]];
        $options["discount_amount"] = [$item["discount_amount"]];

        return $options;
    }

    /**
     * Creating a list of label-value elements to be added as datetime attributes
     *
     * @param array $item
     * @return array
     */
    public function getDateTimeOptions(array $item) : array
    {
        return [];
    }

    public function getType(array $item): ?string
    {
        return (string)$item["product_type"];
    }

    /**
     * In case of configurable products - the mapping is done by sku (duplicate rows)
     *
     * @param array $item
     * @return string
     */
    public function getSkuId(array $item): string
    {
        return (string)$item["sku"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getConnectionProperty(array $item): ?string
    {
        return "sku";
    }

    public function getUnitListPrice(array $item): ?float
    {
        return (float)round($item["original_price"], 2);
    }

    public function getUnitSalesPrice(array $item): ?float
    {
        return (float)round($item["price_incl_tax"], 2);
    }

    public function getUnitGrossMargin(array $item): ?float
    {
        if(is_null($item["base_cost"]))
        {
            return null;
        }

        return (float)round($item["base_price"]-$item["base_cost"], 2);
    }

    public function getQuantity(array $item): ?int
    {
        return (int)$item["qty_ordered"];
    }

    public function getTotalListPrice(array $item): ?float
    {
        return (float)round($item["original_price"], 2) * (int)$item["qty_ordered"];
    }

    public function getTotalSalesPrice(array $item): ?float
    {
        return (float)round($item["row_total_incl_tax"], 2);
    }

    public function getTotalGrossMargin(array $item): ?float
    {
        return null;
    }

    public function getStatus(array $item): ?bool
    {
        return null;
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getStatusCode(array $item): ?string
    {
        if($item["qty_canceled"] > 0)
        {
            return "canceled";
        }

        if($item["qty_refunded"] > 0)
        {
            return "refunded";
        }

        if($item["qty_shipped"] > 0)
        {
            return "shipped";
        }

        if($item["qty_invoiced"] > 0)
        {
            return "invoiced";
        }

        return "ordered";
    }


}
