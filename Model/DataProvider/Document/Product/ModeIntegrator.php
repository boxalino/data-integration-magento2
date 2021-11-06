<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeHelperTrait;
use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValueListHelperTrait;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\ModeDisabledException;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;

/**
 * @package Boxalino\DataIntegration\Model\Document\Product
 */
abstract class ModeIntegrator implements DocProductPropertyInterface
{
    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocInstantIntegrationTrait;
    use AttributeValueListHelperTrait;
    use AttributeHelperTrait;

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
        return $this->getSyncCheck() ?? date("Y-m-d H:i", strtotime("-1 hour"));
    }

    /**
     * Review the property handler that uses this data provider in order to access the required return content
     * @return array
     */
    protected function getFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_a_s.value"
        ];
    }

    /**
     * prepare the data provider with additional relevant elements
     */
    public function resolve(): void {}


}
