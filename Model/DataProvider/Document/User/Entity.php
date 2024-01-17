<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\User;

use Boxalino\DataIntegration\Api\DataProvider\DocUserLineInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\User\Entity as DataProviderResourceModel;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingRequiredPropertyException;

/**
 * Class Entity
 * The users exported are as follows:
 * - belong to the website the account is linked to
 */
class Entity extends ModeIntegrator
    implements DocUserLineInterface
{

    /**
     * @param DataProviderResourceModel $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;
        $this->attributeValueNameList = new \ArrayObject();
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        return [
            new \Zend_Db_Expr("c_e.entity_id AS {$this->getDiIdField()}"),
            'c_e.*',
            'c_a_e.*'
        ];
    }

    /**
     * @return int
     */
    public function getEntityTypeId() : int
    {
        return \Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER;
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getType(array $item): ?string
    {
        return null;
    }

    /**
     * @param array $item
     * @return string
     */
    public function getPersonaId(array $item): string
    {
        return (string)$item["entity_id"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getPersonaType(array $item): ?string
    {
        return "customer";
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getInternalId(array $item): ?string
    {
        try{
            $value = (string) $item["entity_id"];
        } catch (\Throwable $exception)
        {
            $value = (string) $item[$this->getDiIdField()];
        }

        if(empty($value))
        {
            throw new MissingRequiredPropertyException("Missing internal_id on content: " . json_encode($item));
        }

        return $value;
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getExternalId(array $item): ?string
    {
        return (string)$item["increment_id"];
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
        return $item["gender"];
    }

    /**
     * @param array $item
     * @return string|null
     * @throws \Exception
     */
    public function getDateOfBirth(array $item): ?string
    {
        return $this->sanitizeDateTimeValue($item["dob"]);
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getAccountCreation(array $item): ?string
    {
        return $this->sanitizeDateTimeValue($item["created_at"]);
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getCreationLabel(array $item): ?string
    {
        return $item["created_in"];
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
        if($item["is_active"]==1)
        {
            return "active";
        }
        return "inactive";
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
        return array_filter([$item["store_id"]]);
    }

    /**
     * @param array $item
     * @return array
     */
    public function getWebsites(array $item): array
    {
        return array_filter([$item["website_id"]]);
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getCompany(array $item): ?string
    {
        return $item["billing_company"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getVat(array $item): ?string
    {
        return $item["taxvat"];
    }

    /**
     * @param array $item
     * @return bool|null
     */
    public function getVatIsValid(array $item): ?bool
    {
        return $item["billing_vat_is_valid"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getVatRequestId(array $item): ?string
    {
        return $item["billing_vat_request_id"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getVatRequestDate(array $item): ?string
    {
        return $item["billing_vat_request_date"];
    }

    /**
     * @param array $item
     * @return bool|null
     */
    public function getVatRequestSuccess(array $item): ?bool
    {
        if(is_null($item["billing_vat_request_success"]))
        {
            return null;
        }
        return (bool)$item["billing_vat_request_success"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getStreet(array $item): ?string
    {
        return $item["billing_street"];
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
        return $item["billing_city"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getZipcode(array $item): ?string
    {
        return $item["billing_postcode"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getStateID(array $item): ?string
    {
        return $item["billing_region_id"];
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
        return $item["billing_region"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getCountryiso(array $item): ?string
    {
        return $item["billing_country_id"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getCountryID(array $item): ?string
    {
        return $item["billing_country_id"];
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getPhone(array $item): ?string
    {
        return $item["billing_telephone"];
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
        return null;
    }

    /**
     * @param array $item
     * @return string|null
     */
    public function getFax(array $item): ?string
    {
        return $item["billing_fax"];
    }


}
