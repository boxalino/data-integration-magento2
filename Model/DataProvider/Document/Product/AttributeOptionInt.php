<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeOption as DataProviderResourceModel;

/**
 * Class AttributeOptionInt
 */
class AttributeOptionInt extends AttributeOptionAbstract
    implements DocProductPropertyListInterface
{
    
    public function getExcludeConditionals(): array
    {
        return ['e_a.source_model IS NULL OR e_a.source_model=\'Magento\\\Eav\\\Model\\\Entity\\\Attribute\\\Source\\\Table\''];
    }

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

    
}
