<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

/**
 * Class TierPrice
 *
 * Export the tier price values as a string (JSON)
 * JSON format: [{customer_group:<value>, values:[{"qty":"<value>", value:"<value">},{"qty":"<value>", value:"<value">},..]}, {}]
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class TierPrice extends StringAttributeAbstract
{

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "tier_price";
    }


}
