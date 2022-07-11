<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class Image
 * The data provider is returning the required data structure required for generating the content for product main image
 * There are 3 image types (image/base, small_image & thumbnail)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Image extends IntegrationPropertyHandlerAbstract
{

    use AttributeConfigurationOnDataProviderTrait;

    public function _getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();
        $dataProvider = $this->getDataProvider();
        
        foreach($dataProvider->getAttributes() as $attribute)
        {
            $this->setAttribute($attribute);
            $this->_addAttributeConfigOnDataProviderByAttribute();
            list($attributeCode, $attributeName) = $this->_getPropertyNameAndAttributeCode();

            /** @var array $item columns: di_id, <attributeCode>, lang1, lang2, lang3 ..  */
            foreach($dataProvider->getData() as $item)
            {
                if($item instanceof \ArrayIterator)
                {
                    $item = $item->getArrayCopy();
                }

                $id = $this->_getDocKey($item);
                if(!isset($content[$id]))
                {
                    $content[$id][$this->getResolverType()] = [];
                }

                $content[$this->_getDocKey($item)][$this->getResolverType()][] = $this->getImagesSchema($item, $languages, $attributeCode);
            }
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_IMAGES;
    }


}
