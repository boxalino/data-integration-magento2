<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class AttributeIntLocalized
 */
class AttributeIntLocalized extends AttributeLocalizedAbstract
{

    public function getBackendTypeList() : array
    {
        return ["int"];
    }

    public function getFrontendInputList() : array
    {
        return ["select", "text", NULL];
    }

    public function getEntityAttributeTableType() : string
    {
        return "int";
    }

}
