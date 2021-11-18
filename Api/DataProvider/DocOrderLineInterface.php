<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocOrderLineInterface
 * doc_order schema match
 */
interface DocOrderLineInterface extends DocOrderPropertyInterface
{

    /**
     * @param array $row
     * @return string
     */
    public function getInternalId(array $row) : string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getExternalId(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getParentId(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getPersonaType(array $row): ?string;

    /**
     * @param array $row
     * @return string
     */
    public function getPersonaId(array $row): string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getOrderSysCd(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getSellerPersonaType(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getSellerPersonaId(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getCurrencyCd(array $row): ?string;

    /**
     * @param array $row
     * @return float
     */
    public function getTotalCrncyAmt(array $row): float;

    /**
     * @param array $row
     * @return float|null
     */
    public function getTotalCrncyAmtNet(array $row): ?float;

    /**
     * @param array $row
     * @return float|null
     */
    public function getTotalGrossMarginCrncyAmt(array $row): ?float;

    /**
     * @param array $row
     * @return float|null
     */
    public function getTotalNetMarginCrncyAmt(array $row): ?float;

    /**
     * @param array $row
     * @return float|null
     */
    public function getShippingCostsNet(array $row): ?float;

    /**
     * @param array $row
     * @return float|null
     */
    public function getCurrencyFactor(array $row): ?float;

    /**
     * @param array $row
     * @return bool|null
     */
    public function getTaxFree(array $row): ?bool;

    /**
     * @param array $row
     * @return float|null
     */
    public function getTaxRate(array $row): ?float;

    /**
     * @param array $row
     * @return float|null
     */
    public function getTaxAmnt(array $row): ?float;

    /**
     * @param array $row
     * @return string|null
     */
    public function getPaymentMethod(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getShippingMethod(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getShippingDescription(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getDevice(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getReferer(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getPartner(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getLanguage(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getTrackingCode(array $row): ?string;

    /**
     * @param array $row
     * @return bool|null
     */
    public function getIsGift(array $row): ?bool;

    /**
     * @param array $row
     * @return bool|null
     */
    public function getWrapping(array $row): ?bool;

    /**
     * @param array $row
     * @return string|null
     */
    public function getEmail(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getCreation(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getLastUpdate(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getConfirmation(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getCleared(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getSent(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getReceived(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getReturned(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getRepaired(array $row): ?string;

    /**
     * @param array $row
     * @return float|null
     */
    public function getStatus(array $row): ?float;

    /**
     * @param array $row
     * @return string|null
     */
    public function getStatusCode(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getInternalState(array $row): ?string;

    /**
     * @param array $row
     * @return string|null
     */
    public function getStore(array $row): ?string;

    /**
     * @param array $row
     * @return float|null
     */
    public function getShippingCosts(array $row): ?float;
    

}
