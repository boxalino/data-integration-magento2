<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocProductPricePropertyInterface
 */
interface DocProductPricePropertyInterface extends DocProductPropertyInterface
{

    public function getListPrice(string $id) : array;

    public function getSalesPrice(string $id) : array;

    public function getCostPrice(string $id) : array;

    public function getOtherPrices(string $id) : array;

}
