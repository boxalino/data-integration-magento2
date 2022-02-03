<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Pricing as PricingSchema;

/**
 * Class Pricing
 *
 * Property specific to the product line & product group
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#PRICING
 *
 * By default, just the default currency values are exported
 * For more options - please customize
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Pricing extends IntegrationPropertyHandlerAbstract
{

    public function getValues(): array
    {
        $content = [];
        $currencyFactors = $this->getCurrencyFactorMap();
        $languages = $this->getSystemConfiguration()->getLanguages();
        $currencyCodes = array_unique($this->getSystemConfiguration()->getCurrencyCodes());

        foreach ($this->getDataProvider()->getData() as $item)
        {
            if($item instanceof \ArrayIterator)
            {
                $item = $item->getArrayCopy();
            }

            $id = $this->_getDocKey($item);

            /** @var PricingSchema $schema */
            $schema = $this->getPricingSchema($languages, $currencyCodes, $currencyFactors,
                $item[$this->getAttributeCode()],
                $this->getDataProvider()->getLabelForPriceByRow($item));

            $content[$id][$this->getResolverType()] = $schema;
        }

        return $content;
    }

    /**
     * For multi-currency stores, set the price in each currency based on the currency factor
     * @return array
     */
    public function getCurrencyFactorMap() : array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_PRICING;
    }


}
