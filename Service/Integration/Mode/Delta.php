<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Mode;

use Boxalino\DataIntegrationDoc\Service\ErrorHandler\FailSyncException;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingConfigurationException;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\DeltaIntegrationTrait;

/**
 * Delta Integration Handler
 * Used for setting DI params on the document handlers (doc_product, doc_attribute_value, doc_order, etc)
 *
 * Update:
 * 1. when MVIEW mode is used, there is no SYNCCHECK request
 * 2. if the number of flagged IDs is bigger than the one of the `fullConversionThreshold` value - the DELTA changes modes to FULL
 */
abstract class Delta extends AbstractIntegrationHandler
{
    use DeltaIntegrationTrait;

    /**
     * @var array
     */
    protected $ids = [];

    public function integrate(): void
    {
        $this->setHandlerIntegrateTime((new \DateTime())->format("Y-m-d H:i:s"));
        $this->reviewModeBasedOnSyncSize(count($this->getIds()));
        $this->addSystemConfigurationOnHandlers();
        $this->integrateDelta();
    }

    public function integrateDelta(): void
    {
        $configuration = $this->getDiConfiguration();
        if(is_null($configuration))
        {
            throw new MissingConfigurationException("Configurations have not been loaded in the " . get_class($this));
        }

        if(count($this->getIds()))
        {
            $this->syncStart();
        }

        /** @var DocHandlerInterface $handler */
        foreach($this->getHandlers() as $handler)
        {
            if($handler instanceof DocHandlerInterface)
            {
                $handler->setDiConfiguration($configuration);
                $handler->integrate();
            }
        }

        $this->sync();
    }

    /**
     * @return string|null
     */
    public function getSyncCheck() : ?string
    {
        $syncCheck = $this->syncCheck();
        if(is_null($syncCheck))
        {
            throw new FailSyncException("There has been no FULL data sync for the account. The delta can not be triggered");
        }

        return $syncCheck;
    }

    /**
     * @param array $ids
     */
    public function setMviewIds(array $ids) : void
    {
        $this->ids = $ids;
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }


}
