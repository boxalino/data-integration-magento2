<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

trait ContextualAttributeTrait
{

    /**
     * Export status/visibility/property value with some contextual dependencies (product type, available active parents/children, etc)
     *
     * @param array $item
     * @return array
     */
    public function getContextData(array $item) : array
    {
        return $this->getDataByCode($this->getDocPropertyNameByContext(), $item[$this->getDiIdField()]);
    }

    /**
     * Export data as is in Magento2 for the given entity id
     *
     * @param array $item
     * @return array
     */
    public function getAsIsData(array $item) : array
    {
        return $this->getDataByCode($this->getDocPropertyNameByContext(false), $item[$this->getDiIdField()]);
    }

    /**
     * @param string $attributeName
     * @param array $fields
     */
    protected function _loadData(string $attributeName, array $fields) : void
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->getResourceModel()->getFetchPairsByFieldsWebsiteStore(
                $fields,
                $this->getSystemConfiguration()->getWebsiteId(),
                $storeId
            );

            $attributeContent = $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        $this->attributeNameValuesList->offsetSet($attributeName, $attributeContent);
    }


}