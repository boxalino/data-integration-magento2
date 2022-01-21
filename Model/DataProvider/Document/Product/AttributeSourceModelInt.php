<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValue\EavAttributeSourceModelTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeGlobal as DataProviderResourceModel;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class AttributeSourceModelInt
 * For global/website values, the content can be loaded with the matching source model label
 */
class AttributeSourceModelInt extends AttributeGlobalAbstract
{
    use EavAttributeSourceModelTrait;

    protected $attributeOptions;

    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        DataProviderResourceModel $resource,
        ObjectManagerInterface $objectManager
    ){
        parent::__construct($resource);
        $this->objectManager = $objectManager;
        $this->attributeOptions = new \ArrayObject();
    }

    /**
     * For each attribute_code configured as $this->propertyCode  - read product_id / value options
     * A row must be returned for each product id
     *
     * @return array
     */
    public function _getData(): array
    {
        $this->loadSourceModelOptions();
        $data = $this->resourceModel->getSelectAllForGlobalAttribute(
            $this->getFields(),
            $this->getSystemConfiguration()->getWebsiteId(),
            $this->getSystemConfiguration()->getStoreIds(),
            $this->getAttributeId(),
            $this->getEntityAttributeTableType()
        );
        
        if($this->attributeOptions->offsetExists($this->getAttributeCode()))
        {
            /** @var \ArrayObject $attributeContent */
            $attributeContent = $this->attributeOptions->offsetGet($this->getAttributeCode());
            foreach($data as &$row)
            {
                $row["value_id"] = $row[$this->getAttributeCode()];
                if($attributeContent->offsetExists($row[$this->getAttributeCode()]))
                {
                    $row[$this->getAttributeCode()] = $attributeContent->offsetGet($row[$this->getAttributeCode()]);
                }
            }
        }

        return $data;
    }

    /**
     * Resolves the localized attribute details for option ids
     */
    protected function loadSourceModelOptions() : void
    {
        $sourceModelClass = $this->resourceModel->getAttributeFieldByAttrCode("source_model", $this->getAttributeCode());
        if(empty($sourceModelClass))
        {
            return;
        }

        $this->attributeOptions->offsetSet(
            $this->getAttributeCode(),
            $this->getSourceModelClassOptions($sourceModelClass)
        );
    }

    public function getBackendTypeList() : array
    {
        return ["int"];
    }

    public function getFrontendInputList() : array
    {
        return ["select"];
    }

    public function getEntityAttributeTableType() : string
    {
        return "int";
    }

    public function getExcludeConditionals(): array
    {
        return ['e_a.source_model != \'Magento\\\Eav\\\Model\\\Entity\\\Attribute\\\Source\\\Table\''];
    }


}
