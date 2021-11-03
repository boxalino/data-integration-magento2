<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeLocalized as DataProviderResourceModel;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class AttributeLocalizedAbstract
 * Abstract class with compact flow for accessing and handling exported content for localized attributes
 */
abstract class AttributeLocalizedAbstract extends ModeIntegrator
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
     * di_id, langValue1, langValue2, langValue3
     *
     * @return array
     */
    public function _getData() : array
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->resourceModel->getFetchParisForLocalizedAttributeByStoreId(
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
     * @return array
     */
    public function getAttributes(): array
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

    /**
     * ex: int, varchar, datetime, text
     *
     * @return string
     */
    abstract function getEntityAttributeTableType() : string;

    abstract function getBackendTypeList() : array;

    abstract function getFrontendInputList() : array;

    protected function getUseOrConditional() : bool
    {
        return false;
    }

    function getDataDelta() : array
    {
       return [];
    }


}
