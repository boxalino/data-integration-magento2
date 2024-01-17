<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class Price
 */
trait SpecialPriceDateTrait
{

    /**
     * Loading special_date_from & special_date_to data
     * @return void
     */
    protected function loadSpecialPriceDateAttributes() : void
    {
        foreach($this->getSpecialDateAttributes() as $attribute)
        {
            if($attribute["is_global"] === ScopedAttributeInterface::SCOPE_STORE)
            {
                $this->attributeNameValuesList->offsetSet(
                    $attribute['attribute_code'],
                    new \ArrayObject($this->getLocalizedDataForAttribute(
                        $this->getFields(), (int)$attribute["attribute_id"], "datetime"
                    ))
                );
                continue;
            }

            $this->attributeNameValuesList->offsetSet(
                $attribute["attribute_code"],
                new \ArrayObject($this->getGlobalDataForAttributeAsLocalized(
                    $this->getFields(), (int)$attribute["attribute_id"], "datetime")
                )
            );
        }
    }

    /**
     * @return array
     */
    protected function getSpecialDateAttributes() : array
    {
        return $this->globalResourceModel->getAttributesByScopeBackendTypeFrontendInput(
            [],[],[],false,[],["special_from_date", "special_to_date"]
        );
    }

    /**
     * @param array $specialFromDate
     * @param array $specialToDate
     * @param array $specialPrice
     * @return array
     */
    protected function _getSpecialPrice(array $specialFromDate, array $specialToDate, array $specialPrice, array $price) : array
    {
        if($this->_comparePriceWith($price, $specialPrice))
        {
            if(empty($specialFromDate) && empty($specialToDate))
            {
                return $specialPrice;
            }

            if($specialToDate && empty($specialFromDate))
            {
                if($this->_compareDateWith($specialToDate))
                {
                    return $specialPrice;
                }

                return [];
            }

            if($specialFromDate && empty($specialToDate))
            {
                if($this->_compareDateWith($specialFromDate, false))
                {
                    return $specialPrice;
                }

                return [];
            }

            if($this->_compareDateWith($specialFromDate, false) && $this->_compareDateWith($specialToDate))
            {
                return $specialPrice;
            }
        }

        return [];
    }

    /**
     * @param array $prices
     * @param array $salesPrices
     * @return bool
     */
    protected function _comparePriceWith(array $prices, array $salesPrices) : bool
    {
        $languages = $this->getSystemConfiguration()->getLanguages();
        $price = array_unique(array_filter(array_values(array_intersect_key($prices, array_flip($languages)))));
        $salesPrice = array_unique(array_filter(array_values(array_intersect_key($salesPrices, array_flip($languages)))));
        if(empty($salesPrice))
        {
            return false;
        }

        if(count($price) == count($salesPrice))
        {
            $price = $price[0];
            $salesPrice = $salesPrice[0];

            return $price > $salesPrice;
        }

        return false;
    }

    /**
     * @param string $date
     * @param bool $more
     * @return bool
     */
    protected function _compareDateWith(array $dates, bool $more = true) : bool
    {
        $languages = $this->getSystemConfiguration()->getLanguages();
        $date = array_unique(array_filter(array_values(array_intersect_key($dates, array_flip($languages)))))[0];
        $currentDate = new \DateTime('now');
        $compareDate = new \DateTime($date);

        if($more)
        {
            return $compareDate->format(DateTime::DATE_PHP_FORMAT) >= $currentDate->format(DateTime::DATE_PHP_FORMAT);
        }

        return $compareDate->format(DateTime::DATE_PHP_FORMAT) <= $currentDate->format(DateTime::DATE_PHP_FORMAT);
    }


}
