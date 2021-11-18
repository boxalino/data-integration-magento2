<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class IndividuallyVisible
 *
 * Used to set the flag on each of the sku/items. Useful scenarios:
 * a child product is to be displayed in listing by itself, next to product grouping
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class IndividuallyVisible extends IntegrationPropertyHandlerAbstract
{

    public function getValues(): array
    {
        $content = [];
        foreach($this->getDataProvider()->getData() as $id=>$value)
        {
            $id = $this->_getDocKey($id);

            $content[$id] = [];
            $content[$id][$this->getAttributeCode()] = (bool)$value;
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_INDIVIDUALLY_VISIBLE;
    }


}
