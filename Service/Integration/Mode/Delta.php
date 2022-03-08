<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Mode;

use Boxalino\DataIntegrationDoc\Service\ErrorHandler\FailSyncException;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\DeltaIntegrationTrait;

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
        $this->addSystemConfigurationOnHandlers();
        $this->integrateDelta();
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
