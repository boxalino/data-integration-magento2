<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPricePropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeGlobal as GlobalDataProviderResourceModel;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeLocalized as LocalizedDataProviderResourceModel;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\TierPrice;

/**
 * Class Price
 */
class Price extends AttributeStrategyAbstract
    implements DocProductPropertyListInterface, DocProductPricePropertyInterface
{
    use SpecialPriceDateTrait;
    use TierPriceTrait;

    /**
     * @var array
     */
    protected $priceAttributes = [];

    /**
     * @param GlobalDataProviderResourceModel | DiSchemaDataProviderResourceInterface $globalResource
     * @param LocalizedDataProviderResourceModel | DiSchemaDataProviderResourceInterface $localizedResource
     */
    public function __construct(
        GlobalDataProviderResourceModel $globalResource,
        LocalizedDataProviderResourceModel $localizedResource,
        TierPrice $tierPriceResource
    ){
        parent::__construct($globalResource, $localizedResource);

        $this->tierPriceResource = $tierPriceResource;
        $this->attributeNameValuesList = new \ArrayObject();
    }

    /**
     *
     * @return array
     */
    public function _getData() : array
    {
        return $this->localizedResourceModel->getFetchAllEntityByFieldsWebsite(
            [
                $this->getDiIdField() => "c_p_e_s.entity_id",
                $this->getAttributeCode() => "c_p_e_s.type_id"
            ],
            $this->getSystemConfiguration()->getWebsiteId()
        );
    }

    /**
     * Preloading relevant content for price modeling
     *
     * - all price attributes (price, special_price, msrp)
     * - special_price_from / special_price_to
     * - tier_price values for all groups (min value)
     */
    public function resolve(): void
    {
        foreach($this->getAttributes() as $attribute)
        {
            $this->_setGetDataStrategy($attribute['is_global']);
            $this->_resolveDataDelta();
            $this->setAttributeId((int)$attribute['attribute_id']);
            $this->setAttributeCode($attribute['attribute_code']);
            if($this->isLocalized)
            {
                $this->attributeNameValuesList->offsetSet(
                    $attribute['attribute_code'],
                    new \ArrayObject($this->getLocalizedDataForAttribute())
                );
                continue;
            }

            $this->attributeNameValuesList->offsetSet(
                $attribute["attribute_code"],
                new \ArrayObject($this->getGlobalDataForAttributeAsLocalized())
            );
        }

        $this->loadSpecialPriceDateAttributes();
        $this->loadTierPriceAllGroups();
    }

    /**
     * Loading necessary attribute details for price properties
     *
     * @return array
     */
    public function getAttributes() : array
    {
        if(empty($this->priceAttributes))
        {
            $this->priceAttributes = $this->globalResourceModel->getAttributesByScopeBackendTypeFrontendInput(
                [],
                ["decimal"],
                ["price"]
            );
        }

        return $this->priceAttributes;
    }

    function getEntityAttributeTableType(): string
    {
        return "decimal";
    }

    /**
     * The origin for the list_price is the generic property "price"
     * @param array $item
     * @return array
     */
    public function getListPrice(array $item): array
    {
        return $this->getDataByCode("price", $item[$this->getDiIdField()]);
    }

    /**
     * The origin for the sales_price is the "special_price" attribute
     *
     * @param array $item
     * @return array
     */
    public function getSalesPrice(array $item): array
    {
        $specialFromDate = $this->getDataByCode("special_from_date", $item[$this->getDiIdField()]);
        $specialToDate = $this->getDataByCode("special_to_date", $item[$this->getDiIdField()]);
        $specialPrice = $this->getDataByCode("special_price", $item[$this->getDiIdField()]);

        return $this->_getSpecialPrice($specialFromDate, $specialToDate, $specialPrice);
    }

    /**
     * The origin for the product cost (msrp)
     *
     * @param array $item
     * @return array
     */
    public function getGrossMarginPrices(array $item): array
    {
        return $this->getDataByCode("msrp", $item[$this->getDiIdField()]);
    }

    /**
     * Tier price for customer_group_id = 0 and min quantity
     *
     * @param array $item
     * @return array
     */
    public function getOtherPrices(array $item): array
    {
        $tierPrices = $this->getDataByCode("tier_price_all_groups", $item[$this->getDiIdField()]);
        if(empty($tierPrices))
        {
            return [];
        }

        $price = $this->getDataByCode("price", $item[$this->getDiIdField()]);
        return $this->_getTierPrice($tierPrices, $price);
    }

    /**
     * @return void
     */
    protected function loadTierPriceAllGroups(): void
    {
        $this->_resolveDataDeltaTierPrice();
        $this->_loadTierPriceAllGroups();
    }


}
