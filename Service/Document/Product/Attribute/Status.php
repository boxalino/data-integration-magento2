<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class Status
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#STATUS
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Status extends IntegrationPropertyHandlerAbstract
{

    function getValues(): array
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
            }

            $content[$id][$this->getAttributeCode()] = $this->getLocalizedSchema($item, $languages);
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
