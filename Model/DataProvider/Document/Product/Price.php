<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPricePropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeGlobal as GlobalDataProviderResourceModel;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeLocalized as LocalizedDataProviderResourceModel;

/**
 * Class Price
 */
class Price extends AttributeStrategyAbstract
    implements DocProductPropertyListInterface, DocProductPricePropertyInterface
{

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
        LocalizedDataProviderResourceModel $localizedResource
    ){
        parent::__construct($globalResource, $localizedResource);

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
     * @param array $item
     * @return array
     */
    public function getSalesPrice(array $item): array
    {
        return $this->getDataByCode("special_price", $item[$this->getDiIdField()]);
    }

    /**
     * The origin for the product cost
     * @param array $item
     * @return array
     */
    public function getGrossMarginPrices(array $item): array
    {
        return [];
    }

    /**
     * Export msrp as other_prices
     *
     * @param array $item
     * @return array
     */
    public function getOtherPrices(array $item): array
    {
        return $this->getDataByCode("msrp", $item[$this->getDiIdField()]);
    }


}
