<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderLineInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Order\Entity as DataProviderResourceModel;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocOrderHandlerInterface;

/**
 * Class Entity
 * The orders exported are as follows:
 * - belong to the website the account is linked to
 */
class Entity extends ModeIntegrator
    implements DocOrderLineInterface
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
    public function _getData(): array
    {
        return $this->getResourceModel()->getFetchAllByFieldsStoreIds($this->getFields(), $this->getSystemConfiguration()->getStoreIds());
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        return [
            new \Zend_Db_Expr("s_o.entity_id AS {$this->getDiIdField()}"),
            's_o.*',
            "s_o_t.*",
            "s_o_p.*",
            "s_s_t.*"
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
        $options["coupon_code"] = [$item["coupon_code"]];
        $options["coupon_rule_name"] = [$item["coupon_rule_name"]];
        $options["tax_title"] = [$item["s_o_t_title"]];
        $options["shipment_carrier_title"] = [$item["s_s_t_title"]];
        if(!empty($item["s_o_p_additional_information"]))
        {
            if(isset(json_decode($item["s_o_p_additional_information"], true)["method_title"]))
            {
                $options["payment_title"] = [json_decode($item["s_o_p_additional_information"], true)["method_title"]];
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
        $options["total_qty_ordered"] = [$item["total_qty_ordered"]];
        $options["total_refunded"] = [$item["total_refunded"]];
        $options["total_item_count"] = [$item["total_item_count"]];
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

    /**
     * @param array $row
     * @return string
     */
    public function getInternalId(array $row) : string
    {
        return (string) $row["entity_id"];
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getExternalId(array $row): ?string
    {
        return (string) $row["increment_id"];
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getParentId(array $row): ?string
    {
        return (string) $row["relation_parent_id"];
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getPersonaType(array $row): ?string
    {
        if((bool)($row["customer_is_guest"]))
        {
            return "guest";
        }

        return "customer";
    }

    /**
     * @param array $row
     * @return string
     */
    public function getPersonaId(array $row): string
    {
        return (string) $row["customer_id"];
    }

    /**
     * By default it is ECM (ecommerce)
     * If content is synced from other sources - must be updated
     * @param array $row
     * @return string|null
     */
    public function getOrderSysCd(array $row): ?string
    {
        return "ECM";
    }

    /**
     * No seller persona type for default ecommerce
     *
     * @param array $row
     * @return string|null
     */
    public function getSellerPersonaType(array $row): ?string
    {
        return null;
    }

    /**
     * No seller persona type for default ecommerce
     *
     * @param array $row
     * @return string|null
     */
    public function getSellerPersonaId(array $row): ?string
    {
        return null;
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getCurrencyCd(array $row): ?string
    {
        return (string) $row["order_currency_code"];
    }

    /**
     * grand_total is the amount the client pays (subtotal + taxes + shipping - discount)
     *
     * @param array $row
     * @return float
     */
    public function getTotalCrncyAmt(array $row): float
    {
        return (float) round($row["grand_total"], 2);
    }

    /**
     * Subtotal is the pre-discounted / pre-taxed order value
     *
     * @param array $row
     * @return float|null
     */
    public function getTotalCrncyAmtNet(array $row): ?float
    {
        return (float) round($row["subtotal"], 2);
    }

    /**
     * Can be calculated based on items` cost details
     *
     * @param array $row
     * @return float|null
     */
    public function getTotalGrossMarginCrncyAmt(array $row): ?float
    {
        return null;
    }

    /**
     * Can be calculated based on items` cost details
     *
     * @param array $row
     * @return float|null
     */
    public function getTotalNetMarginCrncyAmt(array $row): ?float
    {
        return null;
    }

    /**
     * @param array $row
     * @return float|null
     */
    public function getShippingCostsNet(array $row): ?float
    {
        return (float) round($row["shipping_amount"], 2);
    }

    /**
     * @param array $row
     * @return float|null
     */
    public function getCurrencyFactor(array $row): ?float
    {
        return (float) round($row["base_to_order_rate"], 2);
    }

    /**
     * @param array $row
     * @return bool|null
     */
    public function getTaxFree(array $row): ?bool
    {
        return (bool) !((float) $row["tax_amount"] > 0);
    }

    /**
     * @param array $row
     * @return float|null
     */
    public function getTaxRate(array $row): ?float
    {
        if(isset($row["s_o_t_percent"]))
        {
            return (float) round($row["s_o_t_percent"], 2);
        }

        return null;
    }

    /**
     * @param array $row
     * @return float|null
     */
    public function getTaxAmnt(array $row): ?float
    {
        return (float) round($row["tax_amount"], 2);
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getPaymentMethod(array $row): ?string
    {
        return (string) $row["s_o_p_method"] ?? null;
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getShippingMethod(array $row): ?string
    {
        return (string) $row["shipping_method"];
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getShippingDescription(array $row): ?string
    {
        return (string) $row["shipping_description"];
    }

    /**
     * Can be updated project-wise if information is available
     *
     * @param array $row
     * @return string|null
     */
    public function getDevice(array $row): ?string
    {
        return null;
    }

    /**
     * Can be updated project-wise if information is available
     *
     * @param array $row
     * @return string|null
     */
    public function getReferer(array $row): ?string
    {
        return null;
    }

    /**
     * Can be updated project-wise if information is available
     *
     * @param array $row
     * @return string|null
     */
    public function getPartner(array $row): ?string
    {
        return null;
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getLanguage(array $row): ?string
    {
        $storeIdLanguageMap = $this->getSystemConfiguration()->getStoreIdsLanguagesMap();
        if(isset($storeIdLanguageMap[$row["store_id"]]))
        {
            return $storeIdLanguageMap[$row["store_id"]];
        }

        return null;
    }

    /**
     * Tracking details is available in the sales_shipment_track table
     *
     * @param array $row
     * @return string|null
     */
    public function getTrackingCode(array $row): ?string
    {
        return $row["s_s_t_track_number"];
    }

    /**
     * @param array $row
     * @return bool|null
     */
    public function getIsGift(array $row): ?bool
    {
        return isset($row["gift_message_id"]) ? (bool)$row["gift_message_id"] : null;
    }

    /**
     * Can be updated project-wise if information is available
     *
     * @param array $row
     * @return bool|null
     */
    public function getWrapping(array $row): ?bool
    {
        return null;
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getEmail(array $row): ?string
    {
        return (string) $row["customer_email"];
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getCreation(array $row): ?string
    {
        return (string) $this->sanitizeDateTimeValue($row["created_at"]);
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getLastUpdate(array $row): ?string
    {
        return (string) $this->sanitizeDateTimeValue($row["updated_at"]);
    }

    /**
     * Can be updated project-wise if information is available
     *
     * @param array $row
     * @return string|null
     */
    public function getConfirmation(array $row): ?string
    {
        if($row["status"] === "processing" || $row["state"] === "processing")
        {
            return (string)$row["updated_at"];
        }

        return null;
    }

    /**
     * Can be updated project-wise if information is available
     *
     * @param array $row
     * @return string|null
     */
    public function getCleared(array $row): ?string
    {
        return null;
    }

    /**
     * Read data from "sales_shipment" table
     *
     * @param array $row
     * @return string|null
     */
    public function getSent(array $row): ?string
    {
        return $this->sanitizeDateTimeValue((string)$row["s_s_t_created_at"]) ?? null;
    }

    /**
     * Can be updated project-wise if information is available
     *
     * @param array $row
     * @return string|null
     */
    public function getReceived(array $row): ?string
    {
        return null;
    }

    /**
     * Can be updated project-wise if information is available
     *
     * @param array $row
     * @return string|null
     */
    public function getReturned(array $row): ?string
    {
        return null;
    }

    /**
     * Can be updated project-wise if information is available
     *
     * @param array $row
     * @return string|null
     */
    public function getRepaired(array $row): ?string
    {
        return null;
    }

    /**
     * @param array $row
     * @return float|null
     */
    public function getStatus(array $row): ?float
    {
        if($row["state"] === "complete" || $row["state"] === "closed")
        {
            return 1;
        }

        return 0;
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getStatusCode(array $row): ?string
    {
        return (string) $row["state"];
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getInternalState(array $row): ?string
    {
        return (string) $row["status"];
    }

    /**
     * @param array $row
     * @return string|null
     */
    public function getStore(array $row): ?string
    {
        return (string) $row["store_name"];
    }

    /**
     * @param array $row
     * @return float|null
     */
    public function getShippingCosts(array $row): ?float
    {
        return (float) $row["shipping_incl_tax"];
    }



}
