<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

/**
 * Class AttributeDatetime
 *
 * Model for accessing content for the attributes declared with scope global or website and backend_type="datetime"
 * (the static attributes - ex: created_at, updated_at are exported as part of the entity)
 *
 */
class AttributeDatetime extends AttributeGlobalAbstract
{

    public function getBackendTypeList() : array
    {
        return ["datetime"];
    }

    public function getFrontendInputList() : array
    {
        return [];
    }

    public function getEntityAttributeTableType() : string
    {
        return "datetime";
    }

}
