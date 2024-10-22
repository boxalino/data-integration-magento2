<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration;

use Boxalino\DataIntegration\Service\Integration\Mode\AbstractIntegrationHandler;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\GcpRequestInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\CoreIntegrationHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\HandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\TransformerIntegrationTrait;
use Psr\Log\LoggerInterface;

/**
 * CoreIntegrationHandler
 *
 * Used to export RAW csv/json data from the db to the T (transform) service
 * It would make the CORE integration with Boxalino as documented
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/928874497/DI-SAAS+ELT+Flow#The-DI-SAAS--CORE-request
 */
class CoreIntegrationHandler extends AbstractIntegrationHandler
    implements CoreIntegrationHandlerInterface
{
    use TransformerIntegrationTrait;

    public function __construct(
        LoggerInterface $logger,
        array $docHandlers = [],
        int $timeout = 0
    ){
        parent::__construct($logger, [], $timeout, 0);
        $this->logger = $logger;

        foreach($docHandlers as $doc => $resolver)
        {
            if($resolver instanceof HandlerInterface)
            {
                $this->docHandlerList->append($resolver);
            }
        }
        $this->setTimeout($timeout);
    }

    /**
     * @return void
     */
    public function integrate(): void
    {
        $this->setHandlerIntegrateTime((new \DateTime())->format("Y-m-d H:i:s"));
        $this->addSystemConfigurationOnHandlers();
        $this->integrateTransform();
    }

    public function getIntegrationType() : string
    {
        return GcpRequestInterface::GCP_TYPE_CORE;
    }


}
