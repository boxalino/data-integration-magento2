<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console;

use Boxalino\DataIntegrationDoc\Service\Util\ConfigurationDataObject;

/**
 * Class GenericExecuteTrait
 */
trait GenericExecuteTrait
{

    /**
     * Removed the check for account input parameter
     * The mview is saved globally and gets cleared at the end of the flow
     *
     * @return array
     */
    protected function _execute()  : array
    {
        $exceptionMessages = [];
        /** @var ConfigurationDataObject $configuration */
        foreach($this->getConfigurations() as $configuration)
        {
            if($this->canRun($configuration))
            {
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
