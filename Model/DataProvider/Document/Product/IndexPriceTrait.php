<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\IndexPrice;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Trait IndexPriceTrait
 */
trait IndexPriceTrait
{

    /**
     * @var IndexPrice
     */
    protected $indexPriceResource;

    /**
     * @return void
     */
    protected function _loadIndexPriceAllGroups() : void
    {
        $attributeContent = new \ArrayObject();
        $data = $this->indexPriceResource->getFetchAllByFieldsWebsite(
            [
                $this->getDiIdField() => "c_p_e_s.entity_id",
                DocSchemaInterface::FIELD_TYPE => "c_p_e_s.type_id",
                "c_p_e_a_s.price",
                'c_p_e_a_s.min_price',
                'c_p_e_a_s.max_price',
                'c_p_e_a_s.final_price'
            ],
            $this->getSystemConfiguration()->getWebsiteId()
        );
        foreach($data as $row)
        {
            $attributeContent->offsetSet($row[$this->getDiIdField()], new \ArrayObject($row));
        }
        $this->attributeNameValuesList->offsetSet("index_price", $attributeContent);
    }

    /**
     * In case processing is required at the level of resolve
     * @return void
     */
    protected function _resolveDataDeltaIndexPrice() : void
    {
        if($this->filterByCriteria())
        {
            $this->indexedPriceResource->useDelta(true);
            if(count($this->getIds()) > 0)
            {
                $this->indexedPriceResource->useDeltaIdsConditionals(true);
                $this->indexedPriceResource->addIdsConditional($this->getIds());
            }
            $this->indexedPriceResource->addDateConditional($this->_getDeltaSyncCheckDate());
        }
    }


}
