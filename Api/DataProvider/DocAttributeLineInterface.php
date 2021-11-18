<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocAttributeLineInterface
 * doc_attribute content handler
 */
interface DocAttributeLineInterface
{

    /**
     * @param array $row
     * @return string
     */
    public function getCode(array $row) : string;

    /**
     * @param array $row
     * @return array
     */
    public function getLabel(array $row) : array;

    /**
     * @param array $row
     * @return string
     */
    public function getInternalId(array $row) : string;

    /**
     * @param array $row
     * @return array
     */
    public function getAttributeGroup(array $row) : array;

    /**
     * @param array $row
     * @return bool
     */
    public function isMultivalue(array $row) : bool;

    /**
     * @param array $row
     * @return bool
     */
    public function isIndexed(array $row) : bool;

    /**
     * @param array $row
     * @return bool
     */
    public function isLocalized(array $row) : bool;

    /**
     * @param array $row
     * @return string
     */
    public function getFormat(array $row) : string;

    /**
     * @param array $row
     * @return array
     */
    public function getDataTypes(array $row): array;

    /**
     * @param array $row
     * @return bool
     */
    public function isHierarchical(array $row) : bool;

    /**
     * @param array $row
     * @return int
     */
    public function getSearchBy(array $row) : int;

    /**
     * @param array $row
     * @return bool
     */
    public function isSearchSuggestion(array $row) : bool;

    /**
     * @param array $row
     * @return bool
     */
    public function isFilterBy(array $row) : bool;

    /**
     * @param array $row
     * @return bool
     */
    public function isOrderBy(array $row) : bool;

    /**
     * @param array $row
     * @return bool
     */
    public function isGroupBy(array $row) : bool;


}
