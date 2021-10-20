<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeGlobal as DataProviderResourceModel;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class AttributeGlobalAbstract
 */
abstract class AttributeGlobalAbstract extends ModeIntegrator
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
        return [ScopedAttributeInterface::SCOPE_GLOBAL, ScopedAttributeInterface::SCOPE_WEBSITE];
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
     *
     * @return array
     */
    public function getDataForAttribute() : array
    {
        return $this->resourceModel->getValuesForGlobalAttribute(
            $this->getFields(),
            $this->getSystemConfiguration()->getWebsiteId(),
            $this->getSystemConfiguration()->getStoreIds(),
            $this->getAttributeId(),
            $this->getEntityAttributeTableType()
        );
    }

    /**
     * Review the property handler that uses this data provider in order to access the required return content
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
