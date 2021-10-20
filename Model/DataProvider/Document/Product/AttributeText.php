<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class AttributeText
 */
class AttributeText extends AttributeGlobalAbstract
{

    public function getBackendTypeList() : array
    {
        return ["text"];
    }

    public function getFrontendInputList() : array
    {
        return [];
    }

    public function getEntityAttributeTableType() : string
    {
        return "text";
    }

}
