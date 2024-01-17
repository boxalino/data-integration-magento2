<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\User;

use Boxalino\DataIntegration\Api\DataProvider\DocUserPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\ConfigurationHelperTrait;

/**
 * @package Boxalino\DataIntegration\Model\DataProvider\Document\User
 */
abstract class ModeIntegrator implements DocUserPropertyInterface
{

    use ConfigurationHelperTrait;

    /**
     * @var \ArrayObject
     */
    protected $attributeValueNameList;

    /**
     * @return int
     */
    abstract function getEntityTypeId() : int;

    /**
     * @return array
     */
    abstract protected function getFields() : array;

    /**
     * Access property data (internal flow)
     * Can be used as base for additional logics (delta, instant, etc)
     *
     * @return array
     */
    public function _getData(): array
    {
        return $this->getResourceModel()->getFetchAllByFieldsWebsiteId($this->getFields(), $this->getSystemConfiguration()->getWebsiteId());
    }

    /**
     * pre-load the varchar,int,datetime custom user attributes
     */
    public function resolve(): void
    {
        $websiteId = $this->getSystemConfiguration()->getWebsiteId();
        foreach($this->resourceModel->getFetchPairsAttributes($this->getEntityTypeId()) as $code => $table)
        {
            $attributeContent = new \ArrayObject();
            foreach($this->resourceModel->getFetchAllAttributeContent($code, $this->getEntityTypeId(), $table, $websiteId) as $row)
            {
                $availableDiData = new \ArrayObject();
                if($attributeContent->offsetExists($row["entity_id"]))
                {
                    $availableDiData = $attributeContent->offsetGet($row["entity_id"]);
                }
                $attrContent = [];
                if($availableDiData->offsetExists($code))
                {
                    $attrContent = $availableDiData->offsetGet($code);
                }
                $attrContent[] = $row["value"];
                $availableDiData->offsetSet($code, $attrContent);
                $attributeContent->offsetSet($row["entity_id"], $availableDiData);
            }

            $this->attributeValueNameList->offsetSet($table, $attributeContent);
            unset($attrContent); unset($availableDiData);
        }
    }

    /**
     * Creating a list of label-value elements to be added as string attributes
     * backend_type="varchar" or "text"
     * @param array $item
     * @return array
     */
    public function getStringOptions(array $item) : array
    {
        return $this->_getOptionsByBackendTypeList(["varchar","text"], $item);
    }

    /**
     * Creating a list of label-value elements to be added as numeric attributes
     * backend_type="int" or "decimal"
     * @param array $item
     * @return array
     */
    public function getNumericOptions(array $item) : array
    {
        return $this->_getOptionsByBackendTypeList(["int","decimal"], $item);
    }

    /**
     * Creating a list of label-value elements to be added as datetime attributes
     * backend_type="datetime"
     * @param array $item
     * @return array
     */
    public function getDateTimeOptions(array $item) : array
    {
        return $this->_getOptionsByBackendTypeList(["datetime"], $item);
    }

    /**
     * @param array $backendTypeList
     * @param array $item
     * @return array
     */
    protected function _getOptionsByBackendTypeList(array $backendTypeList, array $item) : array
    {
        $options = [];
        foreach($backendTypeList as $type)
        {
            if($this->attributeValueNameList->offsetExists($type))
            {
                /** @var \ArrayObject $typeContent */
                $typeContent = $this->attributeValueNameList->offsetGet($type);
                if($typeContent->offsetExists($item[$this->getDiIdField()]))
                {
                    /** @var \ArrayObject $itemContent */
                    $itemContent = $typeContent->offsetGet($item[$this->getDiIdField()]);
                    foreach($itemContent->getArrayCopy() as $propertyName => $values)
                    {
                        $options[$propertyName] = $values;
                    }
                }
            }
        }

        return $options;
    }



}
