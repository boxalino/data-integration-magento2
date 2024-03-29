<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class AttributeAbstract
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
abstract class AttributeAbstract extends IntegrationPropertyHandlerAbstract
{

    use AttributeConfigurationOnDataProviderTrait;

    /**
     * The flow for accessing/loading different <type>_attributes properties is same
     *
     * @return array
     */
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
            $this->logDebug("$attributeCode reviewed for export.");

            if($this->breakLoop())
            {
                continue;
            }

            $productsWithAttributeValues = 0;
            /** @var array $item columns di_id, <attributeCode> with value */
            foreach($dataProvider->getData() as $id => $item)
            {
                $itemList = [$item];
                if($item instanceof \ArrayIterator)
                {
                    $itemList = [$item->getArrayCopy()];
                }

                if($item instanceof \ArrayObject)
                {
                    $isSingleValue = false;
                    $itemList = $item->getArrayCopy();
                }

                foreach($itemList as $row)
                {
                    if($row instanceof \ArrayIterator)
                    {
                        $row = $row->getArrayCopy();
                    }

                    $id = $this->_getDocKey($row);
                    if(!isset($content[$id][$this->getDocSchemaPropertyNode()]))
                    {
                        $content[$id][$this->getDocSchemaPropertyNode()] = [];
                    }

                    try{
                        $content[$id][$this->getDocSchemaPropertyNode()][] =
                            $this->getSchema($row, $languages, $attributeName, $attributeCode)->toArray();
                    } catch (\Throwable $exception)
                    {
                        $this->logWarning("Error on ". $this->getResolverType() . " for $attributeCode with exception: "
                            . $exception->getMessage() . " on " . json_encode($row)
                        );
                    }
                }
                $productsWithAttributeValues++;
            }

            $this->logDebug("$attributeCode is used by $productsWithAttributeValues products");
        }

        $this->logInfo(count($content) . " items have content for " . $this->getResolverType());

        return $content;
    }

    /**
     * @param array $item
     * @param array $languages
     * @param string $attributeName
     * @param string $attributeCode
     * @return DocPropertiesInterface
     */
    abstract function getSchema(array $item, array $languages, string $attributeName, string $attributeCode) : DocPropertiesInterface;

    /**
     * @return bool
     */
    protected function breakLoop() : bool
    {
        if(in_array($this->attribute['attribute_code'], $this->getSkipAttributeCodeList()))
        {
            return true;
        }

        if(in_array($this->attribute['frontend_input'], $this->getSkipFrontendInputTypeList()))
        {
            return true;
        }

        return false;
    }

    /**
     * Can be customized for every attribute-type property handler
     * (ex: do not export price, visibility, name, description, title which are isolated properties)
     *
     * @return array
     */
    public function getSkipAttributeCodeList() : array
    {
        return [
            DocSchemaInterface::FIELD_PRICE,
            DocSchemaInterface::FIELD_STATUS,
            DocSchemaInterface::FIELD_TITLE,
            DocSchemaInterface::FIELD_DESCRIPTION,
            DocSchemaInterface::FIELD_SHORT_DESCRIPTION,
            DocSchemaInterface::FIELD_LINK,
            DocSchemaInterface::FIELD_VISIBILITY,
            DocSchemaInterface::FIELD_CATEGORIES,
            DocSchemaInterface::FIELD_STOCK,
            DocSchemaInterface::FIELD_IMAGES,
            DocSchemaInterface::FIELD_BRANDS,
            DocSchemaInterface::FIELD_SUPPLIERS,
            "name", "image"
        ];
    }

    /**
     * @return string[]
     */
    public function getSkipFrontendInputTypeList() : array
    {
        return [
            "media_image"
        ];
    }


}
