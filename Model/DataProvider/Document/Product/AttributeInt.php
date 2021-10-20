<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class AttributeInt
 */
class AttributeInt extends AttributeGlobalAbstract
{

    public function getBackendTypeList() : array
    {
        return ["int"];
    }

    public function getFrontendInputList() : array
    {
        return ["boolean", "text", NULL];
    }

    public function getEntityAttributeTableType() : string
    {
        return "int";
    }

}
