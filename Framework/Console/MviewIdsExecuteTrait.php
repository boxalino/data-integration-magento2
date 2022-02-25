<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console;


use Boxalino\DataIntegration\Service\ErrorHandler\EmptyBacklogException;
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
            try{
                if($this->canRun($configuration))
                {
                    try{
                        $this->getIntegrationHandler()->setMviewIds($this->getMviewIds());
                    } catch (\Throwable $exception)
                    {
                        $this->logger->info("Declared handler can not be used with the mview integration.");
                    }

                    try{
                        if(empty($account))
                        {
                            $this->integrate($configuration);
                            continue;
                        }
                    } catch (\Throwable $exception)
                    {
                        $exceptionMessages[] = $exception->getMessage() . " for " . $this->getProcessName();
                        $this->logger->info($exception->getMessage());
                    }

                    if($configuration->getAccount() == $account)
                    {
                        $this->integrate($configuration);
                        break;
                    }
                }
            } catch (\Throwable $exception)
            {
                $exceptionMessages[] = $exception->getMessage() . " for " . $this->getProcessName();
                $this->logger->alert($exception->getMessage());
            }
        }

        return $exceptionMessages;
    }


}
