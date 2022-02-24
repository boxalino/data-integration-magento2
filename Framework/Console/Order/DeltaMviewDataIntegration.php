<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console\Order;

use Boxalino\DataIntegration\Api\Mview\DiViewHandlerInterface;
use Boxalino\DataIntegration\Framework\Console\AbstractMviewDataIntegration;
use Boxalino\DataIntegration\Framework\Console\MviewIdsExecuteTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Mode\Configuration\FullTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Type\OrderTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\OrderDeltaIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DeltaMviewDataIntegration
 *
 * Used as a CLI command
 * ex: php bin/magento boxalino:di:delta:mview:order [account]
 *
 * Has been re-defined in the magento2-module in order to allow the use of a custom log handler
 * If the integration deltas are handled by MVIEW - use this event in order to clear the mview after a succesfull sync
 */
class DeltaMviewDataIntegration extends AbstractMviewDataIntegration
{

    use FullTrait;
    use OrderTrait;
    use MviewIdsExecuteTrait;

    public function __construct(
        OrderDeltaIntegrationHandlerInterface $integrationHandler,
        DiViewHandlerInterface $diViewHandler,
        LoggerInterface $logger,
        DiConfigurationInterface $configurationManager,
        string $mviewViewId,
        ?string $mviewGroupId = "boxalino_di"
    ){
        parent::__construct($diViewHandler, $logger, $configurationManager, $mviewViewId, $mviewGroupId);
        $this->integrationHandler = $integrationHandler;
    }

    public function getDescription(): string
    {
        return "Boxalino Delta Mview Order Data Integration. Accepts parameters [account]";
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'boxalino:di:delta:mview:order';
    }


}
