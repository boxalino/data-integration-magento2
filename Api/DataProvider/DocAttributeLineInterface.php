<?php
namespace Boxalino\DataIntegration\Api\DataProvider;

/**
 * Interface DocAttributeLineInterface
 * doc_attribute content handler
 */
interface DocAttributeLineInterface
{

    public function getCode(array $row) : string;

    public function getLabel(array $row) : array;

    public function getInternalId(array $row) : string;

    public function getAttributeGroup(array $row) : array;

    public function isMultivalue(array $row) : bool;

    public function isIndexed(array $row) : bool;

    public function isLocalized(array $row) : bool;

    public function getFormat(array $row) : string;

    public function getDataTypes(array $row): array;

    public function isHierarchical(array $row) : bool;

    public function getSearchBy(array $row) : int;

    public function isSearchSuggestion(array $row) : bool;

    public function isFilterBy(array $row) : bool;

    public function isOrderBy(array $row) : bool;

    public function isGroupBy(array $row) : bool;


}
