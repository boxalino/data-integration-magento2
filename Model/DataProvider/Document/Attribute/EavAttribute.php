<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Attribute;

use Boxalino\DataIntegration\Api\DataProvider\DocAttributeLineInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Attribute\EavAttribute as DataProviderResourceModel;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Magento\Eav\Api\Data\AttributeGroupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * EavAttribute Magento2 logic for identifying attribute settings
 */
class EavAttribute implements
    DiSchemaDataProviderInterface,
    DocAttributeLineInterface
{

    use DiIntegrationConfigurationTrait;

    /**
     * @var DataProviderResourceModel
     */
    private $resourceModel;

    /**
     * @var \ArrayObject
     */
    private $attributeList;

    /**
     * @param DataProviderResourceModel $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;

        /** @var \ArrayObject attributeNameValuesList */
        $this->attributeList = new \ArrayObject();
    }

    public function getData(): array
    {
        return $this->resourceModel->getEavAttributes();
    }

    public function resolve() : void {}

    public function getCode(array $row) : string
    {
        return (string)$row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::ATTRIBUTE_CODE];
    }

    public function getLabel(array $row) : array
    {
        $content = [];
        foreach($this->getSystemConfiguration()->getLanguages() as $language)
        {
            $content[$language] = $row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::FRONTEND_LABEL];
        }

        return $content;
    }

    public function getInternalId(array $row) : string
    {
        return (string)$row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::ATTRIBUTE_ID];
    }

    public function getAttributeGroup(array $row) : array
    {
        $content = [];
        foreach($this->getSystemConfiguration()->getLanguages() as $language)
        {
            $content[$language] = (string)$row["attribute_group_code"];
        }

        return $content;
    }

    public function isMultivalue(array $row): bool
    {
        return in_array($row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::FRONTEND_INPUT], ["multiselect", "gallery"]);
    }

    public function isIndexed(array $row): bool
    {
        return true;
    }

    public function isLocalized(array $row): bool
    {
        return in_array($row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::FRONTEND_INPUT], ["multiselect"])
            || in_array(
                $row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::KEY_IS_GLOBAL],
                [ScopedAttributeInterface::SCOPE_STORE]
            )
            || ($row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::FRONTEND_INPUT] === "select"
                && $row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::BACKEND_TYPE] === "int"
                && (in_array(
                        $row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::SOURCE_MODEL],
                        ["Magento\Eav\Model\Entity\Attribute\Source\Table", "Magento\Catalog\Model\ResourceModel\Eav\Attribute"]
                    ) || empty($row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::SOURCE_MODEL])
                )
            );
    }

    public function getFormat(array $row): string
    {
        $backendType = $row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::BACKEND_TYPE];
        if($backendType === "datetime" || $row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::FRONTEND_INPUT]==="date")
        {
            return "datetime";
        }

        if($backendType === "decimal" ||
            ($backendType === "int" && $row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::FRONTEND_INPUT]==="boolean")
        ){
            return "numeric";
        }

        return "string";
    }

    public function getDataTypes(array $row): array
    {
        return ["product"];
    }

    public function isHierarchical(array $row): bool
    {
        return false;
    }

    public function getSearchBy(array $row): int
    {
        return (int)$row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::IS_SEARCHABLE];
    }

    public function isSearchSuggestion(array $row): bool
    {
        return (bool)$row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::USED_IN_PRODUCT_LISTING];
    }

    public function isFilterBy(array $row): bool
    {
        return ($row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::IS_FILTERABLE_IN_SEARCH]
            || $row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::IS_FILTERABLE]);
    }

    public function isOrderBy(array $row): bool
    {
        if($row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::BACKEND_TYPE] === "static"
            || $row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::FRONTEND_INPUT] === "date"
        ){
            return true;
        }

        return (bool)$row[\Magento\Catalog\Model\ResourceModel\Eav\Attribute::USED_FOR_SORT_BY];
    }

    public function isGroupBy(array $row): bool
    {
        return false;
    }


}
