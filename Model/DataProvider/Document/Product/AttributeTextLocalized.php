<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class AttributeTextLocalized
 */
class AttributeTextLocalized extends AttributeLocalizedAbstract
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

    /**
     * The attributes that do not have a backend model defined
     * @return array
     */
    public function getExcludeConditionals(): array
    {
        return ['e_a.backend_model != \'Magento\\\Eav\\\Model\\\Entity\\\Attribute\\\Backend\\\ArrayBackend\' OR e_a.backend_model IS NULL'];
    }


}
