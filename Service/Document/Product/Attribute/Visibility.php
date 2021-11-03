<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\Schema\Visibility as VisibilitySchema;

/**
 * Class Visibility
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#VISIBILITY
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Visibility extends IntegrationPropertyHandlerAbstract
{

    function getValues(): array
    {
        $content = [];
        $languages = $this->getSystemConfiguration()->getLanguages();
        foreach ($this->getDataProvider()->getData() as $item)
        {
            if($item instanceof \ArrayIterator)
            {
                $item = $item->getArrayCopy();
            }

            /** @var VisibilitySchema $schema */
            $schema = $this->getVisibilitySchema($languages, $item);
            $content[$this->_getDocKey($item)][$this->getAttributeCode()] = $schema;
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_VISIBILITY;
    }


}
