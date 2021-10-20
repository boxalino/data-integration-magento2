<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class AttributeVarcharLocalized
 */
class AttributeVarcharLocalized extends AttributeLocalizedAbstract
{

    public function getBackendTypeList() : array
    {
        return ["varchar"];
    }

    public function getFrontendInputList() : array
    {
        return [];
    }

    public function getEntityAttributeTableType() : string
    {
        return "varchar";
    }

}
