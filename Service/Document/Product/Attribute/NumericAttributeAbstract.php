<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class NumericAttributeAbstract
 *
 * MUST DEFINE THE UNIQUE NAME OF THE ATTRIBUTE / MAPPING IN THE `public function getResolverType():string`
 * THE SAME NAME IS USED IN di.xml BETWEEN doc handler - property handler - data provider
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute;
 */
abstract class NumericAttributeAbstract extends IntegrationPropertyHandlerAbstract
{

    public function _getValues(): array
    {
        $content = [];

        /** @var array $item columns: di_id, <attribute_code> as value ..  */
        foreach($this->getDataProvider()->getData() as $item)
        {
            $id = $this->_getDocKey($item);
            if(!isset($content[$id]))
            {
                $content[$id][$this->getDocSchemaPropertyNode()] = [];
            }

            $content[$id][$this->getDocSchemaPropertyNode()][] = $this->getSchema($item);
        }

        return $content;
    }

    /**
     * @param array $item
     * @return DocPropertiesInterface
     */
    public function getSchema(array $item): DocPropertiesInterface
    {
        return $this->getNumericAttributeSchema([(string)$item[$this->getAttributeCode()]], $this->getAttributeCode(), null);
    }

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_NUMERIC;
    }


}
