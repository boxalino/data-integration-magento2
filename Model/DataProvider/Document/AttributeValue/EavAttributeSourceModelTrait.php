<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValue;

use \Magento\Framework\ObjectManagerInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

/**
 * Data provider for any product eav-attribute-option relevant information
 * ex: source_model is defined
 */
trait EavAttributeSourceModelTrait
{

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param string $sourceModelClass
     * @param bool $localized
     * @param array $languages
     * @return \ArrayObject|null
     */
    public function getSourceModelClassOptions(string $sourceModelClass, bool $localized = false, array $languages = []) : ?\ArrayObject
    {
        if(in_array($sourceModelClass, $this->getExcludedSourceModels()))
        {
            return null;
        }

        $sourceModel = $this->createSourceModel($sourceModelClass);
        if($sourceModel instanceof SourceInterface)
        {
            return $this->addSourceModelValueToAttributeContent(
                $sourceModel->getAllOptions(),
                $localized,
                $languages
            );
        }

        return null;
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
     *
     * @param array $data
     * @param bool $localized
     * @param array $languages
     * @return \ArrayObject
     */
    protected function addSourceModelValueToAttributeContent(array $data, bool $localized = false, array $languages = []) : \ArrayObject
    {
        $attributeContent = new \ArrayObject();
        foreach($data as $optionData)
        {
            if(count(array_filter([$optionData['value']], 'strlen')))
            {
                if($localized)
                {
                    $content = new \ArrayIterator();
                    foreach($languages as $languageCode)
                    {
                        $content->offsetSet($languageCode, (string)$optionData['label']);
                    }
                    $attributeContent->offsetSet((string)$optionData['value'], $content);
                    continue;
                }

                $attributeContent->offsetSet((string)$optionData['value'], (string)$optionData['label']);
            }
        }

        return $attributeContent;
    }


}
