<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Mode;

use Boxalino\DataIntegrationDoc\Service\Integration\Mode\FullIntegrationTrait;

abstract class Full extends AbstractIntegrationHandler
{
    use FullIntegrationTrait;

    public function integrate(): void
    {
        $this->setHandlerIntegrateTime((new \DateTime())->format("Y-m-d H:i:s"));
        $this->addSystemConfigurationOnHandlers();
        $this->integrateFull();
    }


}
