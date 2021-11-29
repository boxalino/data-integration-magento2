<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Mode;

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
        $this->syncCheck = $this->syncCheck();
        $this->setHandlerIntegrateTime((new \DateTime())->format("Y-m-d H:i:s"));
        $this->addSystemConfigurationOnHandlers();
        $this->integrateDelta();
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
