<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class Stock
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#STOCK
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class Stock extends IntegrationPropertyHandlerAbstract
{

    function getValues(): array
    {
        $content = [];
        foreach ($this->getDataProvider()->getData() as $item)
        {
            $id = $this->_getDocKey($item);
            $stockValue = $item[$this->getAttributeCode()] ?? false;
            if($stockValue === false)
            {
                continue;
            }

            $content[$id][$this->getResolverType()][] = $this->getStockSchema($item[$this->getAttributeCode()], NULL, $item["stock_name"]);
            $content[$id][DocSchemaInterface::FIELD_NUMERIC][] = $this->getNumericAttributeSchema([$item["stock_status"]], "stock_status", null);
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_STOCK;
    }


}
