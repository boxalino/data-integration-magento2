<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductVisibilityPropertyInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Visibility as DataProviderResourceModel;

/**
 * Class Visibility
 * The logic for accessing visibility information, on a default Magento2 setup, it is same as the one for any other attribute
 *
 * For chidren that are not individually visible, the visibility flag will be set accordingly to the parent visibility
 * This is done in order to have the custom grouped/configurable child properties filterable via product_group representation
 */
class Visibility extends ModeIntegrator
    implements DocProductVisibilityPropertyInterface
{

    /**
     * @var \ArrayObject
     */
    protected $attributeNameValuesList;

    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;
        $this->attributeNameValuesList = new \ArrayObject();
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
     * @param array $item
     * @return array
     */
    public function getContextVisibility(array $item) : array
    {
        return $this->getDataByCode($this->getDocPropertyNameByContext(), $item[$this->getDiIdField()]);
    }

    /**
     * @param array $item
     * @return array
     */
    public function getSelfVisibility(array $item) : array
    {
        return $this->getDataByCode($this->getDocPropertyNameByContext(false), $item[$this->getDiIdField()]);
    }

    /**
     * Pre-loading visibility data for each contexts (self and context)
     */
    public function resolve(): void
    {
        $this->_resolveDataDelta();
        
        $this->_loadVisibilityData($this->getDocPropertyNameByContext(), $this->getContextVisibilityFields());
        $this->_loadVisibilityData($this->getDocPropertyNameByContext(false), $this->getSelfVisibilityFields());
    }

    /**
     * @param string $attributeName
     * @param array $fields
     */
    protected function _loadVisibilityData(string $attributeName, array $fields) : void
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->getResourceModel()->getFetchPairsByFieldsWebsiteStore(
                $fields,
                $this->getSystemConfiguration()->getWebsiteId(),
                $storeId
            );

            $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        $this->attributeNameValuesList->offsetSet($attributeName, $attributeContent);
    }

    /**
     * @return array
     */
    protected function getContextVisibilityFields() : array
    {
        $configurableType = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
        $groupedType = \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE;

        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => new \Zend_Db_Expr("
                (CASE
                    WHEN (c_p_e_s.type_id = '{$configurableType}' OR c_p_e_s.type_id = '{$groupedType}') THEN c_p_e_s.entity_value
                    WHEN c_p_e_s.parent_id IS NULL THEN c_p_e_s.entity_value
                    ELSE c_p_e_s.parent_value
                 END
                )"
            )
        ];
    }

    /**
     * @return array
     */
    protected function getSelfVisibilityFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_s.entity_value"
        ];
    }



}
