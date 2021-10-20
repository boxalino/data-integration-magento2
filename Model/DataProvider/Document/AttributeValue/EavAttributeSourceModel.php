<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValue;

use Boxalino\DataIntegration\Api\DataProvider\DocAttributeValueLineInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue\EavAttributeSourceModel as DataProviderResourceModel;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\Data\OptionSourceInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

/**
 * Data provider for any product eav-attribute-option relevant information
 * ex: source_model is defined
 */
class EavAttributeSourceModel implements
    DiSchemaDataProviderInterface,
    DocAttributeValueLineInterface
{

    use DocAttributeValueLineTrait;
    use DiIntegrationConfigurationTrait;

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
        ObjectManagerInterface $objectmanager
    ) {
        $this->resourceModel = $resource;
        $this->objectManager = $objectmanager;

        /** @var \ArrayObject attributeNameValuesList */
        $this->attributeNameValuesList = new \ArrayObject();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->resourceModel->getAttributeCodes();
    }

    /**
     * Loading relevant attribute data
     */
    public function resolve() : void
    {
        $this->resolveSourceModelAttributesData();
    }

    /**
     * @return array
     */
    public function getResolveByAttributeCode(string $attributeCode) : array
    {
        $this->attributeCode = $attributeCode;
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
    protected function resolveSourceModelAttributesData() : void
    {
        foreach($this->resourceModel->getSourceModelAttributeCodeMapping() as $attributeCode => $sourceModelClass)
        {
            if(in_array($sourceModelClass, $this->getExcludedSourceModels()))
            {
                continue;
            }

            $sourceModel = $this->createSourceModel($sourceModelClass);
            if($sourceModel instanceof SourceInterface)
            {
                $this->attributeNameValuesList->offsetSet(
                    $attributeCode,
                    $this->addValueToAttributeContent(
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
    protected function addValueToAttributeContent(array $data) : \ArrayObject
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


}
