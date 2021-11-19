<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

/**
 * Class AttributeOptionText
 *
 * Sync product attributes of type "string_localized_attributes"
 * (ex: the attributes declared with scope store and backend_type="text" in eav_attribute but are of option-multiselect)
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
class AttributeOptionText extends AttributeOptionAbstract
{

    /**
     * @return string
     */
    public function getResolverType(): string
    {
        return "option_text";
    }


}
