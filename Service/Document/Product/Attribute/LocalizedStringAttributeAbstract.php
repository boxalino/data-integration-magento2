<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocPropertiesInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Typed\StringLocalizedAttribute;

/**
 * Class LocalizedStringAttributeAbstract
 *
 * MUST DEFINE THE UNIQUE NAME OF THE ATTRIBUTE / MAPPING IN THE `public function getResolverType():string`
 * THE SAME NAME IS USED IN di.xml BETWEEN doc handler - property handler - data provider
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute;
 */
abstract class LocalizedStringAttributeAbstract extends IntegrationPropertyHandlerAbstract
{

    /**
     * @return array
     */
    public function _getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();

        /** @var array $item columns: di_id, di24h, lang1, lang2, lang3 ..  */
        foreach($this->getDataProvider()->getData() as $item)
        {
            $id = $this->_getDocKey($item);
            if(!isset($content[$id]))
            {
                $content[$id][$this->getDocSchemaPropertyNode()] = [];
            }

            try{
                $content[$id][$this->getDocSchemaPropertyNode()][] = $this->getSchema($item, $languages, $this->getAttributeCode())->toArray();
            } catch (\Throwable $exception)
            {
                $this->logWarning("Error on ". $this->getResolverType() . " with exception: "
                    . $exception->getMessage() . " on " . json_encode($item)
                );
            }
        }

        return $content;

    }

    /**
     * @param array $item
     * @param array $languages
     * @param string $attributeCode
     * @return DocPropertiesInterface
     */
    public function getSchema(array $item, array $languages, string $attributeCode): DocPropertiesInterface
    {
        return $this->schemaGetter()->getRepeatedGenericLocalizedSchema($item, $languages, $attributeCode, new StringLocalizedAttribute(), null);
    }

    /**
     * @return string
     */
    public function getDocSchemaPropertyNode(): string
    {
        return DocSchemaInterface::FIELD_STRING_LOCALIZED;
    }


}
