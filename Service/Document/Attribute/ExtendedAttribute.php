<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Attribute;

use Boxalino\DataIntegration\Model\DataProvider\Document\Product\ReviewSummary;

/**
 * Class ExtendedAttribute
 *
 * @package Boxalino\DataIntegration\Service\Document\Attribute
 */
class ExtendedAttribute extends CustomAttributeAbstract
{

    /**
     * Upon extending this class, add here additional MODEL/data providers
     *
     * @return array
     */
    function getCustomAttributesDefinition() : array
    {
        return [
            new ReviewSummary()
        ];
    }



}
