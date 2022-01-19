<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\DataValidationTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegration\Service\Document\DocMviewDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\ModeDisabledException;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;

/**
 * Abstract class ModeIntegrator
 * Holds the logic for various integration modes (instant, delta, full)
 * 
 * @package Boxalino\DataIntegration\Model\DataProvider\Document\Order
 */
abstract class ModeIntegrator implements DocOrderPropertyInterface
{
    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocMviewDeltaIntegrationTrait;
    use DataValidationTrait;

    /**
     * @var DiSchemaDataProviderResourceInterface
     */
    protected $resourceModel;

    /**
     * Access property data (internal flow)
     *
     * @return array
     */
    abstract function _getData() : array;

    /**
     * @return array
     */
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
        $this->resourceModel->setChunk((int)$this->getSystemConfiguration()->getChunk());

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
     * Review the property handler that uses this data provider in order to access the required return content
     * @return array
     */
    protected function getFields() : array
    {
        return [
            $this->getDiIdField() => "s_o_e_s.entity_id",
            $this->getAttributeCode() => "s_o_e_a_s.value"
        ];
    }

    /**
     * prepare the data provider with additional relevant elements
     */
    public function resolve(): void {}

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
