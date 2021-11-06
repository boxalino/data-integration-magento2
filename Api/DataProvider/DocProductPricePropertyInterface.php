<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocProductPricePropertyInterface
 */
interface DocProductPricePropertyInterface extends DocProductPropertyInterface
{

    public function getListPrice(array $item) : array;

    public function getSalesPrice(array $item) : array;

    public function getGrossMarginPrices(array $item) : array;

    public function getOtherPrices(array $item) : array;

}
