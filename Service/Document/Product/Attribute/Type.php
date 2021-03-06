<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class Type
 * Accessing the product type value (string)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Type extends IntegrationPropertyHandlerAbstract
{

    public function _getValues(): array
    {
        $content = [];
        foreach($this->getDataProvider()->getData() as $item)
        {
            if(is_null($item[$this->getAttributeCode()]))
            {
                continue;
            }

            $content[$this->_getDocKey($item)][$this->getAttributeCode()] = (string)$item[$this->getAttributeCode()];
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_TYPE;
    }


}
