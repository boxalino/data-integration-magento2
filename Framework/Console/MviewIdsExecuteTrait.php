<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console;

use Boxalino\DataIntegration\Service\ErrorHandler\EmptyBacklogException;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\DeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Util\ConfigurationDataObject;

/**
 * Class MviewIdsExecuteTrait
 */
trait MviewIdsExecuteTrait
{

    /**
     * @return array
     * @throws EmptyBacklogException
     */
    protected function _execute()  : array
    {
        $ids = $this->getMviewIds();
        if(empty($ids))
        {
            throw new EmptyBacklogException(
                "Boxalino DI: no mview ids in backlog for {$this->getIntegrationHandler()->getIntegrationType()} run at {$this->getIntegrationHandler()->getTm()}."
            );
        }

        $exceptionMessages = [];

        /** @var ConfigurationDataObject $configuration */
        foreach($this->getConfigurations() as $configuration)
        {
            if($this->canRun($configuration))
            {
                try{
                    if($this->getIntegrationHandler()->getIntegrationMode() === DeltaIntegrationInterface::INTEGRATION_MODE)
                    {
                        $ids = $this->getAffectedIds();
                    }

                    $this->getIntegrationHandler()->setMviewIds($this->getIdsByWebsite($ids, $configuration->getWebsiteId()));
                } catch (\Throwable $exception)
                {
                    $this->logger->info("Declared handler can not be used with the mview integration: " . $exception->getMessage());
                }

                try{
                    $this->integrate($configuration);
                } catch (\Throwable $exception)
                {
                    $exceptionMessages[] = $exception->getMessage() . " for " . $this->getProcessName();
                    $this->logger->info($exception->getMessage());
                }
            }
        }

        return $exceptionMessages;
    }


}
