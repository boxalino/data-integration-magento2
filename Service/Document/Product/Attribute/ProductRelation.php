<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class ProductRelation
 *
 * Property to identify configurable connection to other products
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/254050518/Referenced+Schema+Types#PRODUCT
 *
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class ProductRelation extends IntegrationPropertyHandlerAbstract
{

    function getValues(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return DocSchemaInterface::FIELD_PRODUCT_RELATIONS;
    }


}
