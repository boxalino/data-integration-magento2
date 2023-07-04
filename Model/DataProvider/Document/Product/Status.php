<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductContextualPropertyInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Status as DataProviderResourceModel;

/**
 * Class Status
 * If the status is configurable only at the level of WEBSITE OR GLOBAL - the property is exported as GLOBAL
 * If the status is configurable at the STORE level - the property is exported as LOCALIZED
 *
 * For grouped / configurable products - the product_group status depends on the availability of children to be bought
 * (ex: status, stock, etc)
 *
 * This logic is configurable
 */
class Status extends ModeIntegrator
    implements DocProductContextualPropertyInterface
{


    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;
    }
    
    /**
     * @return array
     */
    public function _getData(): array
    {
        return $this->getResourceModel()->getFetchAllEntityByFieldsWebsite(
            [$this->getDiIdField() => "c_p_e_s.entity_id"],
            $this->getSystemConfiguration()->getWebsiteId()
        );
    }

    /**
     * Export status value with some contextual dependencies (product type, available active parents/children, etc)
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
     * Preloading visibility data for each contexts (self and context)
     */
    public function resolve(): void
    {
        $this->_resolveDataDelta();

        $this->_loadStatusData($this->getDocPropertyNameByContext(false), $this->getAsIsFields());
        $this->_loadStatusData($this->getDocPropertyNameByContext(), $this->getContextFields());

    }

    /**
     * @param string $attributeName
     * @param array $fields
     */
    protected function _loadStatusData(string $attributeName, array $fields) : void
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

    /**
     * @return array
     */
    protected function getAsIsFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_s.value"
        ];
    }

    /**
     * @return array
     */
    protected function getContextFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_s.contextual"
        ];
    }



}
