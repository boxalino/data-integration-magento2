<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocProductPropertyListInterface
 * doc_product properties data handler
 */
interface DocProductPropertyListInterface extends DocProductPropertyInterface
{

    /**
     * If it is a property handler for a group of content - loop through attributes
     *  Accessing attribute information relevant to make decisions in regards to different kinds of exports
     * (attribute_code, backend_type, frontend_input, source_model, is_global, etc)
     *
     * @return array
     */
    public function getAttributes() : array;

}
