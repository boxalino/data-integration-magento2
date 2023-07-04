<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocProductVisibilityPropertyInterface
 */
interface DocProductVisibilityPropertyInterface extends DocProductContextualPropertyInterface
{

    /**
     * Access the visibility of the item itself
     * (array of unique values, for any store)
     *
     * @param array $item
     * @return array
     */
    public function getIndividualVisibility(array $item) : array;


    
}
