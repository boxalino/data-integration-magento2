<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeLocalized as LocalizedDataProviderResourceModel;

/**
 * Class AttributeLocalizedTrait
 */
trait AttributeLocalizedTrait
{

    /**
     * @var LocalizedDataProviderResourceModel | DiSchemaDataProviderResourceInterface
     */
    protected $localizedResourceModel;

    /**
     * For each attribute_code configured as $this->propertyCode  - read product_id / value options
     * A row must be returned for each product id
     * di_id, langValue1, langValue2, langValue3
     *
     * @param array|null $fields
     * @param int|null $attributeId
     * @param string|null $entityTypeTable
     * @return array
     */
    public function getLocalizedDataForAttribute(?array $fields = null, ?int $attributeId = null, ?string $entityTypeTable = null) : array
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->localizedResourceModel->getFetchPairsForLocalizedAttributeByStoreId(
                $fields ?? $this->getFields(),
                $this->getSystemConfiguration()->getWebsiteId(),
                $storeId,
                $attributeId ?? $this->getAttributeId(),
                $entityTypeTable ?? $this->getEntityAttributeTableType()
            );

            $attributeContent = $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        return $attributeContent->getArrayCopy();
    }


}
