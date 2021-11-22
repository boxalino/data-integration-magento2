<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocSchemaTypedInterface
 * Handling the typed content for document lines
 */
interface DocSchemaTypedInterface 
{
    /**
     * Generic getter for the values
     * @param string $propertyName
     * @param array $row
     * @return mixed
     */
    public function get(string $propertyName, array $row);

    /**
     * List of value-label to be added as string attributes on the order item element
     *
     * @param array $item
     * @return mixed
     */
    public function getStringOptions(array $item) : array;

    /**
     * List of value-label to be added as string attributes on the order item element
     *
     * @param array $item
     * @return array
     */
    public function getNumericOptions(array $item) : array;

    /**
     * Creating a list of label-value elements to be added as datetime attributes
     *
     * @param array $item
     * @return array
     */
    public function getDateTimeOptions(array $item) : array;
    

}
