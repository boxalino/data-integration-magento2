<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Typed\StringLocalizedAttribute;

/**
 * Class Status
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#STATUS
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Status extends IntegrationPropertyHandlerAbstract
{

    public function _getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();
        foreach ($this->getDataProvider()->getData() as $id => $item)
        {
            if($item instanceof \ArrayIterator)
            {
                $item = $item->getArrayCopy();
            }
            $id = $this->_getDocKey($item);
            if(!isset($content[$id]))
            {
                $content[$id][$this->getAttributeCode()] = [];
                $content[$id][DocSchemaInterface::FIELD_STRING_LOCALIZED] = [];
            }

            $content[$id][$this->getAttributeCode()] = $this->getLocalizedSchema($item, $languages);
            $content[$id][DocSchemaInterface::FIELD_STRING_LOCALIZED][] = $this->getRepeatedGenericLocalizedSchema(
                $item,
                $languages,
                DocSchemaInterface::DI_SCHEMA_CONTEXTUAL_PROPERTY_PREFIX . $this->getAttributeCode(),
                new StringLocalizedAttribute(), null
            )->toArray();
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_STATUS;
    }


}
