<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderContactPropertyInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Order\Contact as DataProviderResourceModel;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocOrderHandlerInterface;

/**
 * Class Contact
 * Handles the billing/shipping details about the order
 */
class Contact extends ModeIntegrator
    implements DocOrderContactPropertyInterface
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
            's_o_a.*',
            "c_e.*"
        ];
    }

    /**
     * @param array $item
     * @return array
     */
    public function getStringOptions(array $item): array
    {
        return [];
    }

    /**
     * @param array $item
     * @return array
     */
    public function getNumericOptions(array $item): array
    {
        return [];
    }

    /**
     * @param array $item
     * @return array
     */
    public function getDateTimeOptions(array $item): array
    {
        return [];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getType(array $item): ?string
    {
        return $item["address_type"];
    }

    /**
     * @param array $item
     * @return string
     */
    public function getPersonaId(array $item): string
    {
        if(is_null($item["c_e_entity_id"]))
        {
            return "";
        }

        return $item["c_e_entity_id"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getPersonaType(array $item): ?string
    {
        if(is_null($item["c_e_entity_id"]))
        {
            return "guest";
        }

        return "customer";
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getInternalId(array $item): ?string
    {
        return $item["entity_id"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getExternalId(array $item): ?string
    {
        return null;
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getTitle(array $item): ?string
    {
        return null;
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getPrefix(array $item): ?string
    {
        return $item["prefix"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getFirstname(array $item): ?string
    {
        return $item["firstname"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getMiddlename(array $item): ?string
    {
        return $item["middlename"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getLastname(array $item): ?string
    {
        return $item["lastname"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getSuffix(array $item): ?string
    {
        return $item["suffix"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getGender(array $item): ?string
    {
        return $item["c_e_gender"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getDateOfBirth(array $item): ?string
    {
        return $this->sanitizeDateTimeValue($item["c_e_dob"]);
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getAccountCreation(array $item): ?string
    {
        return $this->sanitizeDateTimeValue($item["c_e_created_at"]);
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getCreationLabel(array $item): ?string
    {
        return $item["c_e_created_in"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getAutoGroup(array $item): ?string
    {
        return $item["customer_group_code"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getInvoiceStatus(array $item): ?string
    {
        return null;
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getStatus(array $item): ?string
    {
        return null;
    }

    /**
     * @param array $item
     * @return array
     */
    public function getCustomerGroups(array $item): array
    {
        return array_filter([$item["customer_group_code"]]);
    }

    /**
     * @param array $item
     * @return array
     */
    public function getStores(array $item): array
    {
        return array_filter([$item["c_e_store_id"]]);
    }

    /**
     * @param array $item
     * @return array
     */
    public function getWebsites(array $item): array
    {
        return array_filter([$item["c_e_website_id"]]);
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getCompany(array $item): ?string
    {
        return $item["company"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getVat(array $item): ?string
    {
        return $item["vat_id"];
    }

    /**
     * @param array $item
     * @return bool|null
     */
    public function getVatIsValid(array $item): ?bool
    {
        return $item["vat_is_valid"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getVatRequestId(array $item): ?string
    {
        return $item["vat_request_id"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getVatRequestDate(array $item): ?string
    {
        return $item["vat_request_date"];
    }

    /**
     * @param array $item
     * @return bool|null
     */
    public function getVatRequestSuccess(array $item): ?bool
    {
        return $item["vat_request_success"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getStreet(array $item): ?string
    {
        return $item["street"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getAdditionalAddressLine(array $item): ?string
    {
        return null;
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getCity(array $item): ?string
    {
        return $item["city"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getZipcode(array $item): ?string
    {
        return $item["postcode"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getStateID(array $item): ?string
    {
        return $item["region_id"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getDepartment(array $item): ?string
    {
        return null;
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getStatename(array $item): ?string
    {
        return $item["region"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getCountryiso(array $item): ?string
    {
        return $item["country_id"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getCountryID(array $item): ?string
    {
        return $item["country_id"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getPhone(array $item): ?string
    {
        return $item["telephone"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getEmail(array $item): ?string
    {
        return $item["email"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getMobilePhone(array $item): ?string
    {
        return $item["telephone"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getFax(array $item): ?string
    {
        return $item["fax"];
    }


}
