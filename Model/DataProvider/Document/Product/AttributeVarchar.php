<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class AttributeVarchar
 */
class AttributeVarchar extends AttributeGlobalAbstract
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
