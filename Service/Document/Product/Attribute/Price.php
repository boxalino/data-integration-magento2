<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPricePropertyInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class Price
 *
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#PRICE
 *
 * By default, just the default currency values are exported
 * For more options - please customize
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Price extends IntegrationPropertyHandlerAbstract
{

    public function _getValues(): array
    {
        $content = [];
        $currencyFactor = $this->getCurrencyFactorMap();
        $languages = $this->getSystemConfiguration()->getLanguages();
        $currencyCodes = array_unique($this->getSystemConfiguration()->getCurrencyCodes());
        $dataProvider = $this->getDataProvider();

        if($dataProvider instanceof DocProductPricePropertyInterface)
        {
            foreach ($dataProvider->getData() as $item)
            {
                $listPrice = $dataProvider->getListPrice($item);
                $salesPrice = $dataProvider->getSalesPrice($item);
                if(empty($listPrice) && empty($salesPrice))
                {
                    continue;
                }

                $id = $this->_getDocKey($item);
                $schema = $this->getLocalizedPriceSchema($languages, $currencyCodes, $currencyFactor,
                    $salesPrice, $listPrice,
                    $dataProvider->getGrossMarginPrices($item),
                    $dataProvider->getOtherPrices($item)
                );

                $content[$id][$this->getResolverType()] = $schema;
            }
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_PRICE;
    }

    /**
     * For multi-currency stores, set the price in each currency based on the currency factor
     * @return array
     */
    public function getCurrencyFactorMap() : array
    {
        return [];
    }

}
