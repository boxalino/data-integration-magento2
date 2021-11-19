<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValue;

use Boxalino\DataIntegration\Api\DataProvider\DocAttributeListInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocAttributeValueLineInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeHelperTrait;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\AttributeValue\EavAttributeSourceModel as DataProviderResourceModel;
use \Magento\Framework\ObjectManagerInterface;

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
    use EavAttributeSourceModelTrait;

    /**
     * @var DataProviderResourceModel
     */
    private $resourceModel;

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
            if(is_null($sourceModelClass))
            {
                continue;
            }

            $content = $this->getSourceModelClassOptions($sourceModelClass, true, $this->getSystemConfiguration()->getLanguages());
            if(is_null($content))
            {
                continue;
            }

            $this->attributeNameValuesList->offsetSet(
                $attributeCode,
                $content
            );
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
