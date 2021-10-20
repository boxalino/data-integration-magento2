<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class AttributeDecimalLocalized
 */
class AttributeDecimalLocalized extends AttributeLocalizedAbstract
{

    public function getBackendTypeList() : array
    {
        return ["decimal"];
    }

    public function getFrontendInputList() : array
    {
        return [];
    }

    public function getEntityAttributeTableType() : string
    {
        return "decimal";
    }

}
