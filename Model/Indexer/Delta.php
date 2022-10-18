<?php
namespace Boxalino\DataIntegration\Model\Indexer;

use Boxalino\DataIntegrationDoc\Framework\Integrate\DiIntegrateTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\DiLoggerTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Mode\Configuration\DeltaTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Util\ConfigurationDataObject;
use Psr\Log\LoggerInterface;

/**
 * Class Delta
 * Delta Data Sync : logic for delta sync handlers
 *
 * @package Boxalino\Exporter\Model\Indexer
 */
abstract class Delta implements \Magento\Framework\Indexer\ActionInterface,
    \Magento\Framework\Mview\ActionInterface
{
    use DeltaTrait;
    use DiIntegrateTrait;
    use DiLoggerTrait;

    public function __construct(
        LoggerInterface $logger,
        DiConfigurationInterface $configurationManager
    ){
        $this->configurationManager = $configurationManager;
        $this->logger = $logger;
    }

    /**
     * @param ConfigurationDataObject $configurationDataObject
     * @return bool
     */
    abstract function canRun(ConfigurationDataObject $configurationDataObject): bool;

    /**
     * @param int $id
     */
    public function executeRow($id){}

    /**
     * @param array $ids
     */
    public function executeList(array $ids){}

    /**
     * Run when the MVIEW is in use (Update by Schedule)
     *
     * @param int[] $ids
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($ids)
    {
        $exceptions = [];

        /** @var ConfigurationDataObject $configuration */
        foreach($this->getConfigurations() as $configuration)
        {
            try{
                if($this->canRun($configuration))
                {
                    try{
                        $this->getIntegrationHandler()->setMviewIds($ids);
                    } catch (\Throwable $exception)
                    {
                        $this->logger->info("Declared handler can not be used with the mview integration.");
                    }

                    try{
                        $this->integrate($configuration);
                    } catch (\Throwable $exception)
                    {
                        $exceptions[] = $exception->getMessage() . " for " . $this->getProcessName();
                        $this->logger->info($exception->getMessage());
                    }
                }
            } catch (\Throwable $exception)
            {
                $exceptions[] = $exception->getMessage() . " for " . $this->getProcessName();
                $this->logger->alert($exception->getMessage());
            }
        }

        if(empty($exceptions))
        {
            return;
        }

        throw new \Exception(json_encode($exceptions));
    }

    /**
     * Run via the command line or cron job (Update on Save mode)
     *
     * The delta IDs will be accessed by checking latest updated IDs
     */
    public function executeFull()
    {
        $exceptions = [];

        try{
            /** @var ConfigurationDataObject $configuration */
            foreach($this->getConfigurations() as $configuration)
            {
                if($this->canRun($configuration))
                {
                    try{
                        $this->integrate($configuration);
                    } catch (\Throwable $exception)
                    {
                        $exceptions[] = $exception->getMessage() . " for " . $this->getProcessName();
                        $this->logger->info($exception->getMessage());
                    }
                }
            }
        } catch (\Throwable $exception)
        {
            $exceptions[] = $exception->getMessage() . " for " . $this->getProcessName();
            $this->logger->alert($exception->getMessage());
        }

        if(empty($exceptions))
        {
            return;
        }

        throw new \Exception(json_encode($exceptions));
    }


}
