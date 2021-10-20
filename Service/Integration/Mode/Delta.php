<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Mode;

use Boxalino\DataIntegrationDoc\Service\Integration\Mode\DeltaIntegrationTrait;

abstract class Delta extends AbstractIntegrationHandler
{
    use DeltaIntegrationTrait;

    public function integrate(): void
    {
        $this->integrateDelta();
    }


}
