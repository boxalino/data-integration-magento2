<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\TierPrice;
use Magento\Framework\Stdlib\DateTime;

/**
 * Trait TierPriceTrait
 */
trait TierPriceTrait
{

    /**
     * @var TierPrice
     */
    protected $tierPriceResource;

    protected function _loadTierPriceAllGroups() : void
    {
        $attributeContent = new \ArrayObject();
        $data = $this->tierPriceResource->getFetchPairsByAllGroupsMinQty($this->getSystemConfiguration()->getWebsiteId());
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $attributeContent = $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        $this->attributeNameValuesList->offsetSet("tier_price_all_groups", $attributeContent);
    }

    /**
     * @param array $tierPrices
     * @param array $price
     * @return array
     */
    protected function _getTierPrice(array $tierPrices, array $price) : array
    {
        if(empty($tierPrices) || empty($price))
        {
            return [];
        }

        array_walk($tierPrices,
            function(&$tierPrice, $language) use ($price)
            {
                if(is_null($tierPrice))
                {
                    return;
                }

                if(strpos($tierPrice, "%") === FALSE)
                {
                    return round((float)$tierPrice, 2);
                }
                
                if(isset($price[$language]))
                {
                    $tierPrice = (string) round((float)$price[$language] * (100 - (int)rtrim($tierPrice, "%")) / 100, 2);
                }
            }
        );

        return $tierPrices;
    }

    /**
     * In case processing is required at the level of resolve
     * @return void
     */
    protected function _resolveDataDeltaTierPrice() : void
    {
        if($this->filterByCriteria())
        {
            $this->tierPriceResource->useDelta(true);
            if(count($this->getIds()) > 0)
            {
                $this->tierPriceResource->useDeltaIdsConditionals(true);
                $this->tierPriceResource->addIdsConditional($this->getIds());
            }
            $this->tierPriceResource->addDateConditional($this->_getDeltaSyncCheckDate());
        }
    }


}
