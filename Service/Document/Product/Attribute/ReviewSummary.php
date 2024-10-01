<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

/**
 * Class ReviewSummary
 *
 * Exports the global rating information (percent)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class ReviewSummary extends NumericAttributeAbstract
{

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "review_summary";
    }


}
