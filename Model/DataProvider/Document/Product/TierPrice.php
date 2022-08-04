<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\TierPrice as DataProviderResourceModel;

/**
 * Class TierPrice
 *
 * Configured tier-prices are saved in the catalog_product_entity_tier_price table
 * The values are configured per website view (not localized)
 */
class TierPrice extends ModeIntegrator
{
    use TierPriceTrait;

    /**
     * @param DataProviderResourceModel | | DiSchemaDataProviderResourceInterface $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ){
        $this->resourceModel = $resource;
        $this->attributeNameValuesList = new \ArrayObject();
    }

    /**
     *
     * @return array
     */
    public function _getData() : array
    {
        return $this->getResourceModel()->getFetchAllByFieldsWebsiteId(
            [
                $this->getDiIdField() => "e.entity_id",
                $this->getAttributeCode() => "e.value"
            ],
            $this->getSystemConfiguration()->getWebsiteId()
        );
    }


}
