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

    public function getExcludeConditionals(): array
    {
        return ['e_a.source_model IS NULL OR e_a.source_model=\'Magento\\\Eav\\\Model\\\Entity\\\Attribute\\\Source\\\Boolean\''];
    }

}
