<?php
namespace Boxalino\DataIntegration\Api\DataProvider;


use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;

/**
 * Interface DocAttributeValueLineInterface
 * doc_attribute_value content handler
 */
interface DocAttributeValueLineInterface extends DiSchemaDataProviderInterface
{
    /**
     * @param string $id
     * @return string
     */
    public function getAttributeName(string $id): string;

    /**
     * @param string $id
     * @return bool
     */
    public function isNumerical(string $id) : bool;

    /**
     * @param string $id
     * @return array
     */
    public function getValueLabel(string $id) : array;

    /**
     * @param string $id
     * @return array
     */
    public function getParentValueIds(string $id) : array;

    /**
     * @param string $id
     * @return array
     */
    public function getShortDescription(string $id) : array;

    /**
     * @param string $id
     * @return array
     */
    public function getDescription(string $id) : array;

    /**
     * @param string $id
     * @return array
     */
    public function getImages(string $id) : array;

    /**
     * @param string $id
     * @return array
     */
    public function getLink(string $id) : array;

    /**
     * @param string $id
     * @return array
     */
    public function getStatus(string $id) : array;

    /**
     * @param string $id
     * @return string | null
     */
    public function getAdmin(string $id) : ?string;


}
