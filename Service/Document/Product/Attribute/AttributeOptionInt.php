<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

/**
 * Class AttributeOption
 *
 * Sync product attributes of type "string_localized_attributes"
 * (ex: the attributes declared with scope store and backend_type="int" in eav_attribute but are of option-select)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class AttributeOptionInt extends AttributeOptionAbstract
{
    
    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "option_int";
    }


}
