<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValue;

use Boxalino\DataIntegration\Api\DataProvider\DocAttributeValueLineInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue\Category as DataProviderResourceModel;

/**
 * Data provider for any category-relevant information
 */
class Category implements
    DiSchemaDataProviderInterface,
    DocAttributeValueLineInterface
{

    use DiIntegrationConfigurationTrait;
    use DocAttributeValueLineTrait;

    /**
     * @var DataProviderResourceModel
     */
    private $resourceModel;

    /**
     * @param DataProviderResourceModel $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;

        /** @var \ArrayObject attributeNameValuesList */
        $this->attributeNameValuesList = new \ArrayObject();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->resourceModel->getEntityByRootCategoryId($this->getSystemConfiguration()->getRootCategoryId());
    }

    /**
     * Loading relevant attribute data
     */
    public function resolve() : void
    {
        $this->resolveLocalizedAttributesData();
        $this->resolveLinkData();
        $this->resolveParentIdsData();
    }

    /**
     * @param string $id
     * @return string
     */
    public function getAttributeName(string $id): string
    {
        return DocSchemaInterface::FIELD_CATEGORIES;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function isNumerical(string $id): bool
    {
        return true;
    }

    /**
     * @param string $id
     * @return array
     */
    public function getValueLabel(string $id): array
    {
        return $this->getDataByCode("name", $id);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getParentValueIds(string $id): array
    {
        return $this->getDataByCode("path", $id);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getShortDescription(string $id): array
    {
        return $this->getDataByCode("meta_description", $id);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getDescription(string $id): array
    {
        return $this->getDataByCode("description", $id);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getImages(string $id): array
    {
        return $this->getDataByCode("image", $id);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getLink(string $id): array
    {
        return $this->getDataByCode("request_path", $id);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getStatus(string $id): array
    {
        return $this->getDataByCode("is_active", $id);
    }

    /**
     * Adds category localized link data
     */
    protected function resolveLinkData() : void
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->resourceModel->getUrlRewriteByTypeStoreId("category", $storeId);
            $this->addValueToAttributeContent($data, $attributeContent, $languageCode);
        }

        $this->attributeNameValuesList->offsetSet("request_path", $attributeContent);
    }

    /**
     * Adds the parent IDs details
     */
    protected function resolveParentIdsData() : void
    {
        $attributeContent = new \ArrayObject();
        $data = $this->resourceModel->getEntityColumnByRootCategoryId($this->getSystemConfiguration()->getRootCategoryId(), "path");
        foreach($data as $id => $value)
        {
            $content = new \ArrayIterator(array_diff(array_filter(explode("/", $value ?? "")), [$id]));
            $attributeContent->offsetSet($id, $content);
        }

        $this->attributeNameValuesList->offsetSet("path", $attributeContent);
    }

    /**
     * Resolves the localized attribute details for category
     */
    protected function resolveLocalizedAttributesData() : void
    {
        foreach($this->getLocalizedAttributeNameTableMapping() as $attributeCode => $attributeTable)
        {
            $attributeContent = new \ArrayObject();
            foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
            {
                $data = $this->resourceModel->getAttributeValueByAttributeTableStoreId($attributeCode, $attributeTable, $storeId);
                $this->addValueToAttributeContent($data, $attributeContent, $languageCode);
            }

            $this->attributeNameValuesList->offsetSet($attributeCode, $attributeContent);
        }
    }

    /**
     * Internal, used to load content
     *
     * @return string[]
     */
    protected function getLocalizedAttributeNameTableMapping() : array
    {
        return [
            "name" => "catalog_category_entity_varchar",
            "description" => "catalog_category_entity_varchar",
            "meta_description" => "catalog_category_entity_text",
            "is_active" => "catalog_category_entity_int"
        ];
    }

    /**
     * @param array $data
     * @param \ArrayObject &$attributeContent
     * @param string $languageCode
     */
    protected function addValueToAttributeContent(array $data, \ArrayObject &$attributeContent, string $languageCode)
    {
        foreach($data as $id => $value)
        {
            $content = new \ArrayIterator();
            if($attributeContent->offsetExists($id))
            {
                $content = $attributeContent->offsetGet($id);
            }
            $content->offsetSet($languageCode, $value);
            $attributeContent->offsetSet($id, $content);
        }
    }


}
