<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class StringAttributeAbstract
 *
 * MUST DEFINE THE UNIQUE NAME OF THE ATTRIBUTE / MAPPING IN THE `public function getResolverType():string`
 * THE SAME NAME IS USED IN di.xml BETWEEN doc handler - property handler - data provider
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
abstract class StringAttributeAbstract extends IntegrationPropertyHandlerAbstract
{

    /**
     * @return array
     */
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

            try{
                $content[$id][$this->getDocSchemaPropertyNode()][] = $this->getSchema($item)->toArray();
            } catch (\Throwable $exception)
            {
                $this->logWarning("Error on ". $this->getResolverType() . "with exception: "
                    . $exception->getMessage() . " on " . json_encode($item)
                );
            }
        }

        $this->logInfo(count($content) . " items have content for " . $this->getResolverType());
        return $content;
    }

    /**
     * @param array $item
     * @return DocPropertiesInterface
     */
    public function getSchema(array $item): DocPropertiesInterface
    {
        return $this->schemaGetter()->getStringAttributeSchema([(string)$item[$this->getAttributeCode()]], $this->getAttributeCode(), null);
    }

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_STRING;
    }


}
