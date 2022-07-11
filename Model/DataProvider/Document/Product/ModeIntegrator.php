<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeHelperTrait;
use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValueListHelperTrait;
use Boxalino\DataIntegration\Model\DataProvider\Document\DataValidationTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegration\Service\Document\DocMviewDeltaIntegrationTrait;
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
    use DocMviewDeltaIntegrationTrait;
    use AttributeValueListHelperTrait;
    use AttributeHelperTrait;
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
     * To be extended with the delta filter logic
     * @return array
     */
    public function getDataDelta() : array
    {
        $this->_resolveDataDelta();
        return $this->_getData();
    }

    /**
     * To be extended with the istant filter logic
     * @return array
     */
    public function getDataInstant() : array
    {
        $this->_resolveDataInstant();
        return $this->_getData();
    }

    /**
     * In case processing is required at the level of resolve
     * @return void
     */
    protected function _resolveDataDelta() : void
    {
        if($this->filterByCriteria())
        {
            $this->getResourceModel()->useDelta(true);
            if(count($this->getIds()) > 0)
            {
                $this->getResourceModel()->useDeltaIdsConditionals(true);
                $this->getResourceModel()->addIdsConditional($this->getIds());
            }
            $this->getResourceModel()->addDateConditional($this->_getDeltaSyncCheckDate());
        }
    }

    /**
     * In case processing is required at the level of resolve
     * @return void
     */
    protected function _resolveDataInstant() : void
    {
        if($this->filterByIds())
        {
            $this->getResourceModel()->useInstant(true);
            if(count($this->getIds()) > 0)
            {
                $this->getResourceModel()->addIdsConditional($this->getIds());
            }
        }
    }

    /**
     * @return DiSchemaDataProviderResourceInterface
     */
    public function getResourceModel() : DiSchemaDataProviderResourceInterface
    {
        return $this->resourceModel;
    }

    public function getData() : array
    {
        /** for delta requests */
        if($this->filterByCriteria())
        {
            return $this->getDataDelta();
        }

        /** for instant requests */
        if($this->filterByIds())
        {
            return $this->getDataInstant();
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
