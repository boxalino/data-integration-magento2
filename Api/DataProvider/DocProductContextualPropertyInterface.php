<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocProductVisibilityPropertyInterface
 */
interface DocProductContextualPropertyInterface extends DocProductPropertyInterface
{

    /**
     * Access the visibility of the item in relationship to the context
     * (ex: as part of a configurable product, as part of a grouped product, etc)
     *
     * @param array $item
     * @return array
     */
    public function getContextData(array $item) : array;

    /**
     * Access the visibility of the item itself
     * (regardless of connection to parent/related products)
     *
     * @param array $item
     * @return array
     */
    public function getAsIsData(array $item) : array;


    
}
