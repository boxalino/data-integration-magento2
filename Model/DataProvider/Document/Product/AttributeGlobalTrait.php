<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeGlobal as GlobalDataProviderResourceModel;

/**
 * Class AttributeGlobalTrait
 */
trait AttributeGlobalTrait
{

    /**
     * @var GlobalDataProviderResourceModel | DiSchemaDataProviderResourceInterface
     */
    protected $globalResourceModel;

    /**
     * Used to create attribute data response structure (localized) when attribute is global
     *
     * @param array|null $fields
     * @param int|null $attributeId
     * @param string|null $entityTypeTable
     * @return array
     */
    public function getGlobalDataForAttributeAsLocalized(?array $fields = null, ?int $attributeId = null, ?string $entityTypeTable = null) : array
    {
        $attributeContent = new \ArrayObject();

        $data = $this->globalResourceModel->getFetchPairsForGlobalAttribute(
            $fields ?? $this->getFields(),
            $this->getSystemConfiguration()->getWebsiteId(),
            $this->getSystemConfiguration()->getStoreIds(),
            $attributeId ?? $this->getAttributeId(),
            $entityTypeTable ?? $this->getEntityAttributeTableType()
        );

        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        return $attributeContent->getArrayCopy();
    }


}
