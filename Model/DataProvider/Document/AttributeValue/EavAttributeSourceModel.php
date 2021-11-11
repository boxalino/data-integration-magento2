<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValue;

use Boxalino\DataIntegration\Api\DataProvider\DocAttributeListInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocAttributeValueLineInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeHelperTrait;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue\EavAttributeSourceModel as DataProviderResourceModel;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

/**
 * Data provider for any product eav-attribute-option relevant information
 * ex: source_model is defined
 */
class EavAttributeSourceModel implements
    DiSchemaDataProviderInterface,
    DocAttributeValueLineInterface,
    DocAttributeListInterface
{

    use DocAttributeValueLineTrait;
    use DiIntegrationConfigurationTrait;
    use AttributeHelperTrait;

    /**
     * @var DataProviderResourceModel
     */
    private $resourceModel;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param DataProviderResourceModel $resource
     */
    public function __construct(
        DataProviderResourceModel $resource,
        ObjectManagerInterface $objectManager
    ) {
        $this->resourceModel = $resource;
        $this->objectManager = $objectManager;

        /** @var \ArrayObject attributeNameValuesList */
        $this->attributeNameValuesList = new \ArrayObject();
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->resourceModel->getFetchColAttributeByFieldsFrontendInputTypes(
            ["attribute_code"], $this->getFrontendInputTypes()
        );
    }

    /**
     * Loading relevant attribute data
     */
    public function resolve() : void
    {
        $this->loadSourceModelAttributesData();
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        if($this->attributeNameValuesList->offsetExists($this->attributeCode))
        {
            $attributeValues = $this->attributeNameValuesList->offsetGet($this->attributeCode);
            return $attributeValues->getArrayCopy();
        }

        return [];
    }

    /**
     * Resolves the localized attribute details for option ids
     */
    protected function loadSourceModelAttributesData() : void
    {
        foreach($this->resourceModel->getFetchPairsAttributeByFieldsFrontendInputTypes(
            ['attribute_code', 'source_model'],$this->getFrontendInputTypes()) as $attributeCode => $sourceModelClass
        ){
            if(in_array($sourceModelClass, $this->getExcludedSourceModels()) || is_null($sourceModelClass))
            {
                continue;
            }

            $sourceModel = $this->createSourceModel($sourceModelClass);
            if($sourceModel instanceof SourceInterface)
            {
                $this->attributeNameValuesList->offsetSet(
                    $attributeCode,
                    $this->addSourceModelValueToAttributeContent(
                        $sourceModel->getAllOptions()
                    )
                );
            }
        }
    }

    /**
     * Default / already exported otherwise source models which are ignored from processing
     *
     * @return string[]
     */
    protected function getExcludedSourceModels() : array
    {
        return ["Magento\Eav\Model\Entity\Attribute\Source\Table"];
    }

    /**
     * @param string $class
     * @return mixed
     */
    protected function createSourceModel(string $class)
    {
        return $this->objectManager->create($class);
    }

    /**
     * The labels are translated in front-end via __()
     * @param array $data
     * @return \ArrayObject
     */
    protected function addSourceModelValueToAttributeContent(array $data) : \ArrayObject
    {
        $attributeContent = new \ArrayObject();
        foreach($data as $optionData)
        {
            if($optionData['value'])
            {
                $content = new \ArrayIterator();
                foreach($this->getSystemConfiguration()->getLanguages() as $languageCode)
                {
                    $content->offsetSet($languageCode, (string)$optionData['label']);
                }

                $attributeContent->offsetSet($optionData['value'], $content);
            }
        }

        return $attributeContent;
    }

    /**
     * @return string[]
     */
    public function getFrontendInputTypes() : array
    {
        return ["multiselect", "select"];
    }


}
