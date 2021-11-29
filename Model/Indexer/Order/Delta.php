<?php
namespace Boxalino\DataIntegration\Model\Indexer\Order;

use Boxalino\DataIntegrationDoc\Framework\Integrate\Type\OrderTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\OrderDeltaIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Delta
 * Delta Data Sync : exports to DI the orders updated within a configurable time frame
 *
 * @package Boxalino\Exporter\Model\Indexer
 */
class Delta extends \Boxalino\DataIntegration\Model\Indexer\Delta
    implements \Magento\Framework\Indexer\ActionInterface,
    \Magento\Framework\Mview\ActionInterface
{
    use OrderTrait;    
    /**
     * Exporter ID in configuration
     */
    const INDEXER_ID = 'boxalino_di_delta_order';

    /**
     * DI type
     */
    const INDEXER_TYPE = 'di_delta_order';

    /**
     * @var OrderDeltaIntegrationHandlerInterface
     */
    protected $integrationHandler;

    public function __construct(
        LoggerInterface $logger,
        DiConfigurationInterface $configurationManager,
        OrderDeltaIntegrationHandlerInterface $integrationHandler
    ){
        $this->integrationHandler = $integrationHandler;
        parent::__construct($logger, $configurationManager);
    }

}
