<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class AttributeOptionLocalized
 *
 * Sync product attributes of type "string_localized_attributes"
 * (ex: the attributes declared with scope store and backend_type="int" in eav_attribute but are of option-select)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class AttributeOption extends AttributeAbstract
{

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
                if($item instanceof \ArrayIterator)
                {
                    $item = $item->getArrayCopy();
                }

                $id = $this->_getDocKey($item);
                if(!isset($content[$id][$this->getDocSchemaPropertyNode()]))
                {
                    $content[$id][$this->getDocSchemaPropertyNode()] = [];
                }

                try{
                    $content[$id][$this->getDocSchemaPropertyNode()][] =
                        $this->getSchema($item, $languages, $attributeName, $attributeCode);
                } catch (\Throwable $exception)
                {
                    $this->logWarning("Error on ". $this->getResolverType() . " for $attributeCode with exception: "
                        . $exception->getMessage() . " on " . json_encode($item)
                    );
                }
                $productsWithAttributeValues++;
            }

            $this->logDebug("$attributeCode is used by $productsWithAttributeValues products");
        }

        $this->logInfo(count($content) . " items have content for " . $this->getResolverType());

        return $content;
    }

    public function getSchema(array $item, array $languages, string $attributeName, string $attributeCode): DocPropertiesInterface
    {
        return $this->getRepeatedLocalizedSchema($item, $languages, $attributeName, $this->getDiIdField());
    }

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_STRING_LOCALIZED;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "option_localized";
    }


}
