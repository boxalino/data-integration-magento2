<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class Status
 * If the status is configurable only at the level of WEBSITE OR GLOBAL - the property is exported as GLOBAL
 * If the status is configurable at the STORE level - the property is exported as LOCALIZED
 */
class Status extends AttributeStrategyAbstract
{

    function getEntityAttributeTableType(): string
    {
        return "int";
    }

}
