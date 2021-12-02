<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeGlobal as DataProviderResourceModel;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class AttributeGlobalAbstract
 */
abstract class AttributeGlobalAbstract extends ModeIntegrator
    implements DocProductPropertyListInterface
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
     * For each attribute_code configured as $this->propertyCode  - read product_id / value options
     * A row must be returned for each product id
     *
     * @return array
     */
    public function _getData(): array
    {
        return $this->resourceModel->getSelectAllForGlobalAttribute(
            $this->getFields(),
            $this->getSystemConfiguration()->getWebsiteId(),
            $this->getSystemConfiguration()->getStoreIds(),
            $this->getAttributeId(),
            $this->getEntityAttributeTableType()
        );
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->resourceModel->getAttributesByScopeBackendTypeFrontendInput(
            $this->getScopeList(),
            $this->getBackendTypeList(),
            $this->getFrontendInputList(),
            $this->getUseOrConditional(),
            $this->getExcludeConditionals()
        );
    }

    /**
     * @return array
     */
    protected function getScopeList() : array
    {
        return [ScopedAttributeInterface::SCOPE_GLOBAL, ScopedAttributeInterface::SCOPE_WEBSITE];
    }

    abstract function getEntityAttributeTableType() : string;
    
    abstract function getBackendTypeList() : array;

    abstract function getFrontendInputList() : array;

    protected function getUseOrConditional() : bool
    {
        return false;
    }

    protected function getExcludeConditionals() : array
    {
        return [];
    }


}
