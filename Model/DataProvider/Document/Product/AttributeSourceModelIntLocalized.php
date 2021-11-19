<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class AttributeSourceModelIntLocalized
 */
class AttributeSourceModelIntLocalized extends AttributeLocalizedAbstract
{
    
    public function getBackendTypeList() : array
    {
        return ["int"];
    }

    public function getFrontendInputList() : array
    {
        return ["select"];
    }

    public function getEntityAttributeTableType() : string
    {
        return "int";
    }

    public function getExcludeConditionals(): array
    {
        return ['e_a.source_model != \'Magento\\\Eav\\\Model\\\Entity\\\Attribute\\\Source\\\Table\''];
    }

    
}
