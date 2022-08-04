<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocProductPricePropertyInterface
 */
interface DocProductPricePropertyInterface extends DocProductPropertyInterface
{

    /**
     * @param array $item
     * @return array
     */
    public function getListPrice(array $item) : array;

    /**
     * @param array $item
     * @return array
     */
    public function getSalesPrice(array $item) : array;

    /**
     * @param array $item
     * @return array
     */
    public function getGrossMarginPrices(array $item) : array;

    /**
     * @param array $item
     * @return array
     */
    public function getOtherPrices(array $item) : array;


}
