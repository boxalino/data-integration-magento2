<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console;

use Boxalino\DataIntegrationDoc\Service\Util\ConfigurationDataObject;

/**
 * Class GenericExecuteTrait
 */
trait GenericExecuteTrait
{

    /**
     * @return array
     */
    protected function _execute()  : array
    {
        $exceptionMessages = [];
        /** @var ConfigurationDataObject $configuration */
        foreach($this->getConfigurations() as $configuration)
        {
            try{
                if($this->canRun($configuration))
                {
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
                $exceptionMessages[] = $exception->getMessage();
            }
        }
        
        return $exceptionMessages;
    }

    
}
