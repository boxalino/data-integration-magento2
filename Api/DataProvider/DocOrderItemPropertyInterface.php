<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocOrderItemPropertyInterface
 * Handling the item information for the doc_order line
 */
interface DocOrderItemPropertyInterface extends DocOrderPropertyInterface
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
    public function getSkuId(array $item): string;

    /**
     * @param array $item
     * @return string|null
     */
    public function getConnectionProperty(array $item): ?string;

    /**
     * @param array $item
     * @return float|null
     */
    public function getUnitListPrice(array $item): ?float;

    /**
     * @param array $item
     * @return float|null
     */
    public function getUnitSalesPrice(array $item): ?float;

    /**
     * @param array $item
     * @return float|null
     */
    public function getUnitGrossMargin(array $item): ?float;

    /**
     * @param array $item
     * @return int|null
     */
    public function getQuantity(array $item): ?int;

    /**
     * @param array $item
     * @return float|null
     */
    public function getTotalListPrice(array $item): ?float;

    /**
     * @param array $item
     * @return float|null
     */
    public function getTotalSalesPrice(array $item): ?float;

    /**
     * @param array $item
     * @return float|null
     */
    public function getTotalGrossMargin(array $item): ?float;

    /**
     * @param array $item
     * @return bool|null
     */
    public function getStatus(array $item): ?bool;

    /**
     * @param array $item
     * @return string|null
     */
    public function getStatusCode(array $item): ?string;
    

}
