<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class AttributeVarcharLocalized
 */
class AttributeVarcharLocalized extends AttributeLocalizedAbstract
{

    public function getBackendTypeList() : array
    {
        return ["varchar", "string"];
    }

    public function getFrontendInputList() : array
    {
        return [];
    }

    public function getEntityAttributeTableType() : string
    {
        return "varchar";
    }

    /**
     * The attributes that have a back-end model defined and are varchar
     * @return array
     */
    public function getExcludeConditionals(): array
    {
        return ['e_a.backend_model != \'Magento\\\Eav\\\Model\\\Entity\\\Attribute\\\Backend\\\ArrayBackend\' OR e_a.backend_model IS NULL'];
    }


}
