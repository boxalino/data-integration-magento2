<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class IndividuallyVisible
 *
 * Used to set the flag on each of the sku/items. Useful scenarios:
 * 1. a child product is to be displayed in listing by itself, next to product grouping
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class IndividuallyVisible extends IntegrationPropertyHandlerAbstract
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
        return DocSchemaInterface::FIELD_INDIVIDUALLY_VISIBLE;
    }


}
