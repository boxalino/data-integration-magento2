<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\DocOrderPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\DataValidationTrait;
use Boxalino\DataIntegration\Model\DataProvider\Document\DocPropertyAccessorTrait;
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
    use DocPropertyAccessorTrait;

    /**
     * @var DiSchemaDataProviderResourceInterface
     */
    protected $resourceModel;

    /**
     * Access property data (internal flow)
     * @return array
     */
    public function _getData(): array
    {
        return $this->getResourceModel()->getFetchAllByFieldsStoreIds($this->getFields(), $this->getSystemConfiguration()->getStoreIds());
    }

    /**
     * @return array
     */
    abstract protected function getFields() : array;

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
            $this->getResourceModel()->useDeltaIdsConditionals(true);
            $this->getResourceModel()->addIdsConditional($this->getIds());
        }
        $this->getResourceModel()->addDateConditional($this->_getDeltaSyncCheckDate());

        return $this->_getData();
    }

    /**
     * The chunk is the last added record, used as a filter logic
     *
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
        $syncCheck = $this->getSyncCheck();
        if(empty($syncCheck))
        {
            return date("Y-m-d H:i", strtotime("-1 week"));
        }

        return date("Y-m-d H:i", strtotime("-5 minutes", strtotime($syncCheck)));
    }

    /**
     * prepare the data provider with additional relevant elements
     */
    public function resolve(): void {}



}
