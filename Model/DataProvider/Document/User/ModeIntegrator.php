<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\User;

use Boxalino\DataIntegration\Api\DataProvider\DocUserPropertyInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\ModeDisabledException;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;

/**
 * @package Boxalino\DataIntegration\Model\DataProvider\Document\User
 */
abstract class ModeIntegrator implements DocUserPropertyInterface
{
    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocInstantIntegrationTrait;


    /**
     * @var \ArrayObject
     */
    protected $attributeValueNameList;

    /**
     * Access property data (internal flow)
     *
     * @return array
     */
    abstract function _getData() : array;

    /**
     * To be extended with the delta filter logic
     * @return array
     */
    abstract function getDataDelta() : array;

    /**
     * @return int
     */
    abstract function getEntityTypeId() : int;


    public function getData() : array
    {
        /** for delta requests */
        if($this->filterByCriteria())
        {
            return $this->getDataDelta();
        }

        /** for instant updates */
        if($this->filterByIds())
        {
            if($this->hasModeEnabled())
            {
                /** @todo to be extended when instant option has been added */
                return [];
            }

            throw new ModeDisabledException("Boxalino DI: instant mode not active. Skipping sync.");
        }

        return $this->_getData();
    }

    /**
     * @return string
     */
    protected function _getDeltaSyncCheckDate() : string
    {
        return $this->getSyncCheck() ?? date("Y-m-d H:i", strtotime("-1 week"));
    }

    /**
     * pre-load the varchar,int,datetime custom attributes
     */
    public function resolve(): void
    {
        $storeIds = $this->getSystemConfiguration()->getStoreIds();
        foreach($this->resourceModel->getFetchPairsAttributes($this->getEntityTypeId()) as $code => $table)
        {
            $attributeContent = new \ArrayObject();
            foreach($this->resourceModel->getFetchAllAttributeContent($code, $this->getEntityTypeId(), $table) as $row)
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
        return $this->_getOptionsByBackendTypeList(["varchar","text"]);
    }

    /**
     * Creating a list of label-value elements to be added as numeric attributes
     * backend_type="int" or "decimal"
     * @param array $item
     * @return array
     */
    public function getNumericOptions(array $item) : array
    {
        return $this->_getOptionsByBackendTypeList(["int","decimal"]);
    }

    /**
     * Creating a list of label-value elements to be added as datetime attributes
     * backend_type="datetime"
     * @param array $item
     * @return array
     */
    public function getDateTimeOptions(array $item) : array
    {
        return $this->_getOptionsByBackendTypeList(["datetime"]);
    }

    /**
     * @param array $backendTypeList
     * @return array
     */
    protected function _getOptionsByBackendTypeList(array $backendTypeList) : array
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

    /**
     * Identify the getter function for the requested property name
     *
     * @param string $propertyName
     * @param array $row
     * @return mixed
     */
    public function get(string $propertyName, array $row)
    {
        $functionSuffix = preg_replace('/\s+/', '', ucwords(implode(" ", explode("_", $propertyName))));
        $functionName = "get" . $functionSuffix;
        $methods = get_class_methods($this);
        $return = null;
        if(in_array($functionName, $methods))
        {
            try{
                $return = $this->$functionName($row);
            } catch (\Throwable $exception)
            {
                throw $exception;
                // do nothing
            }
        }

        return $return;
    }

}
