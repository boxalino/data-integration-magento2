<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocSchemaContactInterface
 * Handling the contact information for the doc_order line
 */
interface DocSchemaContactInterface 
{
    /**
     * @param array $item
     * @return string|null
     */
    public function getType(array $item): ?string;

    /**
     * @param array $item
     * @return string
     */
    public function getPersonaId(array $item): string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getPersonaType(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getInternalId(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getExternalId(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getTitle(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getPrefix(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getFirstname(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getMiddlename(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getLastname(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getSuffix(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getGender(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getDateOfBirth(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getAccountCreation(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getCreationLabel(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getAutoGroup(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getInvoiceStatus(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getStatus(array $item): ?string;

    /**
     * @param array $item
     * @return array
     */
    public function getCustomerGroups(array $item): array;

    /**
     * @param array $item
     * @return array
     */
    public function getStores(array $item): array;

    /**
     * @param array $item
     * @return array
     */
    public function getWebsites(array $item): array;

    /**
     * @param array $item
     * @return string|null
     */
    public function getCompany(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getVat(array $item): ?string;

    /**
     * @param array $item
     * @return bool|null
     */
    public function getVatIsValid(array $item): ?bool;

    /**
     * @param array $item
     * @return string|null
     */
    public function getVatRequestId(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getVatRequestDate(array $item): ?string;

    /**
     * @param array $item
     * @return bool|null
     */
    public function getVatRequestSuccess(array $item): ?bool;

    /**
     * @param array $item
     * @return string|null
     */
    public function getStreet(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getAdditionalAddressLine(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getCity(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getZipcode(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getStateID(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getDepartment(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getStatename(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getCountryiso(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getCountryID(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getPhone(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getEmail(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getMobilePhone(array $item): ?string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getFax(array $item): ?string;
    

}
