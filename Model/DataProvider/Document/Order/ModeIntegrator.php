<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderPropertyInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\ModeDisabledException;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;

/**
 * @package Boxalino\DataIntegration\Model\DataProvider\Document\Order
 */
abstract class ModeIntegrator implements DocOrderPropertyInterface
{
    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocInstantIntegrationTrait;

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
