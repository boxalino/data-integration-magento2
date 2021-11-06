<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeLocalized as LocalizedDataProviderResourceModel;

/**
 * Class AttributeLocalizedTrait
 */
trait AttributeLocalizedTrait
{

    /**
     * @var LocalizedDataProviderResourceModel
     */
    protected $localizedResourceModel;

    /**
     * For each attribute_code configured as $this->propertyCode  - read product_id / value options
     * A row must be returned for each product id
     * di_id, langValue1, langValue2, langValue3
     *
     * @return array
     */
    public function getLocalizedDataForAttribute() : array
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->localizedResourceModel->getFetchPairsForLocalizedAttributeByStoreId(
                $this->getFields(),
                $this->getSystemConfiguration()->getWebsiteId(),
                $storeId,
                $this->getAttributeId(),
                $this->getEntityAttributeTableType()
            );

            $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        return $attributeContent->getArrayCopy();
    }


}
