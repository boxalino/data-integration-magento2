<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Mode;

use Boxalino\DataIntegrationDoc\Service\Integration\Mode\InstantIntegrationTrait;

/**
 *
 */
abstract class Instant extends AbstractIntegrationHandler
{
    use InstantIntegrationTrait;

    public function integrate(): void
    {
        $this->setHandlerIntegrateTime((new \DateTime())->format("Y-m-d H:i:s"));
        $this->addSystemConfigurationOnHandlers();
        $this->integrateInstant();
    }

    /**
     * @param array $ids
     */
    public function setMviewIds(array $ids) : void
    {
        $this->ids = $ids;
    }


}
