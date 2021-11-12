<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeOption as DataProviderResourceModel;

/**
 * Class AttributeOptionVarchar
 */
class AttributeOptionVarchar extends AttributeOptionAbstract
    implements DocProductPropertyListInterface
{

    public function getExcludeConditionals(): array
    {
        return ['e_a.backend_model = \'Magento\\\Eav\\\Model\\\Entity\\\Attribute\\\Backend\\\ArrayBackend\''];
    }

    public function getEntityAttributeTableType() : string
    {
        return "varchar";
    }

    public function getBackendTypeList() : array
    {
        return ["varchar"];
    }

    public function getFrontendInputList() : array
    {
        return ["multiselect"];
    }

    /**
     * In the case of the varchar-multiselect options, the values are divided by ","
     * @return bool
     */
    protected function getAttributeValueAsList(): bool
    {
        return true;
    }


}
