<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValue;

use Boxalino\DataIntegration\Api\DataProvider\DocAttributeValueLineInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeHelperTrait;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue\EavAttributeOption as DataProviderResourceModel;

/**
 * Data provider for any product eav-attribute-option relevant information
 *
 * ex: backend_model Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend / NULL
 * ex: source_model Magento\Eav\Model\Entity\Attribute\Source\Table / NULL
 */
class EavAttributeOption implements
    DiSchemaDataProviderInterface,
    DocAttributeValueLineInterface
{

    use DocAttributeValueLineTrait;
    use DiIntegrationConfigurationTrait;
    use AttributeHelperTrait;

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
        return $this->resourceModel->getFetchPairsOptionIdAttributeCodeByFrontendInputTypes($this->getFrontendInputTypes());
    }

    /**
     * Loading relevant attribute data
     */
    public function resolve() : void
    {
        $this->loadOptionIdTranslation();
    }

    /**
     * Resolves the localized attribute details for option ids
     */
    protected function loadOptionIdTranslation() : void
    {
        foreach($this->resourceModel->getFetchPairsAttributeByFieldsFrontendInputTypes(
            ['attribute_id', 'attribute_code'], $this->getFrontendInputTypes()) as $attributeId => $attributeCode)
        {
            $attributeContent = new \ArrayObject();
            foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
            {
                $data = $this->resourceModel->getFetchPairsAttributeOptionValuesByStoreAndAttributeId($attributeId, $storeId);
                $this->addValueToAttributeContent($data, $attributeContent, $languageCode);
            }

            $this->attributeNameValuesList->offsetSet($attributeCode, $attributeContent);
        }
    }

    /**
     * @return string[]
     */
    public function getFrontendInputTypes() : array
    {
        return ["multiselect", "select"];
    }


}
