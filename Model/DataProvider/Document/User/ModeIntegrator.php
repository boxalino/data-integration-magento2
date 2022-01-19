<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\User;

use Boxalino\DataIntegration\Api\DataProvider\DocUserPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\DataValidationTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegration\Service\Document\DocMviewDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\ModeDisabledException;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;

/**
 * @package Boxalino\DataIntegration\Model\DataProvider\Document\User
 */
abstract class ModeIntegrator implements DocUserPropertyInterface
{
    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocMviewDeltaIntegrationTrait;
    use DataValidationTrait;


    /**
     * @var \ArrayObject
     */
    protected $attributeValueNameList;

    /**
     * @var DiSchemaDataProviderResourceInterface
     */
    protected $resourceModel;

    /**
     * Access property data (internal flow)
     * Can be used as base for additional logics (delta, instant, etc)
     *
     * @return array
     */
    abstract function _getData() : array;

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

        return $this->_getData();
    }

    /**
     * To be extended with the delta filter logic
     * 
     * @return array
     */
    public function getDataDelta() : array
    {
        $this->getResourceModel()->useDelta(true);
        if(count($this->getIds()) > 0)
        {
            $this->getResourceModel()->useDateIdsConditionals(true);
            $this->getResourceModel()->addIdsConditional($this->getIds());
        }
        $this->getResourceModel()->addDateConditional($this->_getDeltaSyncCheckDate());

        return $this->_getData();
    }

    /**
     * @return DiSchemaDataProviderResourceInterface
     */
    public function getResourceModel() : DiSchemaDataProviderResourceInterface
    {
        $this->resourceModel->setBatchSize((int)$this->getSystemConfiguration()->getBatchSize());
        $this->resourceModel->setChunk((string)$this->getSystemConfiguration()->getChunk());

        return $this->resourceModel;
    }

    /**
     * @return string
     */
    protected function _getDeltaSyncCheckDate() : string
    {
        return $this->getSyncCheck() ?? date("Y-m-d H:i", strtotime("-1 week"));
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
