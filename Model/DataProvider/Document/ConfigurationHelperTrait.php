<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegration\Service\Document\DocMviewDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;

/**
 * Integration configuration
 */
trait ConfigurationHelperTrait
{

    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocInstantIntegrationTrait;
    use DocMviewDeltaIntegrationTrait;
    use DocPropertyAccessorTrait;

    /**
     * @var DiSchemaDataProviderResourceInterface
     */
    protected $resourceModel;

    /**
     * Access property data (internal flow)
     * @return array
     */
    abstract public function _getData(): array;

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

        /** for instant requests */
        if($this->filterByIds())
        {
            return $this->getDataInstant();
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
     * To be extended with the istant filter logic
     * @return array
     */
    public function getDataInstant() : array
    {
        $this->getResourceModel()->useInstant(true);
        if(count($this->getIds()) > 0)
        {
            $this->getResourceModel()->addIdsConditional($this->getIds());
        }

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


}
