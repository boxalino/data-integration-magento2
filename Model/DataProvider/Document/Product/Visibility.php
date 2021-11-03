<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class Visibility
 * The logic for accessing visibility information, on a default Magento2 setup, it is same as the one for any other attribute
 */
class Visibility extends AttributeStrategyAbstract
{

    function getEntityAttributeTableType(): string
    {
        return "int";
    }

}
