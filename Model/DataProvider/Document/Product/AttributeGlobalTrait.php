<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeGlobal as GlobalDataProviderResourceModel;

/**
 * Class AttributeGlobalTrait
 */
trait AttributeGlobalTrait
{

    /**
     * @var GlobalDataProviderResourceModel
     */
    protected $globalResourceModel;

    /**
     * Used to create attribute data response structure (localized) when attribute is global
     *
     * @return array
     */
    public function getGlobalDataForAttributeAsLocalized() : array
    {
        $attributeContent = new \ArrayObject();

        $data = $this->globalResourceModel->getFetchPairsForGlobalAttribute(
            $this->getFields(),
            $this->getSystemConfiguration()->getWebsiteId(),
            $this->getSystemConfiguration()->getStoreIds(),
            $this->getAttributeId(),
            $this->getEntityAttributeTableType()
        );

        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        return $attributeContent->getArrayCopy();
    }


}
