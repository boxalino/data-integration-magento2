<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeLocalized as DataProviderResourceModel;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class DatetimeLocalized
 */
abstract class AttributeLocalizedAbstract extends ModeIntegrator
{

    /**
     * @var DataProviderResourceModel
     */
    protected $resourceModel;

    /**
     * @param DataProviderResourceModel $resource
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
        return $this->resourceModel->getAttributesByScopeBackendTypeFrontendInput(
            $this->getScopeList(),
            $this->getBackendTypeList(),
            $this->getFrontendInputList(),
            $this->getUseOrConditional()
        );
    }

    /**
     * @return array
     */
    protected function getScopeList() : array
    {
        return [ScopedAttributeInterface::SCOPE_STORE];
    }

    abstract function getBackendTypeList() : array;

    abstract function getFrontendInputList() : array;

    protected function getUseOrConditional() : bool
    {
        return false;
    }

    abstract function getEntityAttributeTableType() : string;

    public function resolve(): void {}

    /**
     * For each attribute_code configured as $this->propertyCode  - read product_id / value options
     * A row must be returned for each product id
     * di_id, langValue1, langValue2, langValue3
     *
     * @return array
     */
    public function getDataForAttribute() : array
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->resourceModel->getValuesForLocalizedAttributeByStoreId(
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

    /**
     * Review the property handler that uses this data provider in order to access the required return content
     *
     * @return array
     */
    protected function getFields() : array
    {
        return [
            new \Zend_Db_Expr("c_p_e_s.entity_id AS {$this->getDiIdField()}"),
            new \Zend_Db_Expr("c_p_e_a_s.value AS {$this->getAttributeCode()}")
        ];
    }

    function getDataDelta() : array
    {
       return [];
    }


}
