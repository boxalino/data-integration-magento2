<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValueListHelperTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeOption as LocalizedDataProviderResourceModel;

/**
 * Class AttributeLocalizedTrait
 */
trait AttributeOptionTrait
{

    use AttributeValueListHelperTrait;

    /**
     * @var LocalizedDataProviderResourceModel | DiSchemaDataProviderResourceInterface
     */
    protected $optionResourceModel;

    /**
     * @param int $attributeId
     * @return array
     */
    public function getLocalizedOptionValuesByAttributeId(int $attributeId) : \ArrayObject
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->optionResourceModel->getFetchPairsAttributeOptionValuesByStoreAndAttributeId(
                $attributeId, $storeId
            );

            $attributeContent = $this->addValueToAttributeContent($data, $attributeContent, $languageCode);
        }

        return $attributeContent;
    }

    /**
     * @return array
     */
    public function getEntityOptionAttributeData() : array
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->optionResourceModel->getFetchAllForLocalizedAttributeByStoreId(
                $this->getFields(),
                $this->getSystemConfiguration()->getWebsiteId(),
                $storeId,
                $this->getAttributeId(),
                $this->getEntityAttributeTableType()
            );

            $attributeContent = $this->addValueTranslationToAttributeContent(
                $data,
                $attributeContent,
                $this->getAttributeCode(),
                true,
                $this->getAttributeValueAsList()
            );
        }

        return $attributeContent->getArrayCopy();
    }


}
