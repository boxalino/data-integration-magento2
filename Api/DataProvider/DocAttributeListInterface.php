<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;

/**
 * Interface DocAttributeListInterface
 * doc_product properties data handler
 */
interface DocAttributeListInterface extends DiSchemaDataProviderInterface
{

    /**
     * If it is a property handler for a group of content - loop through attributes
     *  Accessing attribute information relevant to make decisions in regards to different kinds of exports
     * (attribute_code, backend_type, frontend_input, source_model, is_global, etc)
     *
     * @return array
     */
    public function getAttributes() : array;

    /**
     * @param string $code
     * @return DiSchemaDataProviderInterface
     */
    public function setAttributeCode(string $code) : DiSchemaDataProviderInterface;

    /**
     * @return string
     */
    public function getAttributeCode() : string;

    /**
     * @param int $id
     * @return DiSchemaDataProviderInterface
     */
    public function setAttributeId(int $id) : DiSchemaDataProviderInterface;

    /**
     * @return int
     */
    public function getAttributeId() : int;

}
