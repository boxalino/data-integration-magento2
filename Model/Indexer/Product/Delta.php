<?php
namespace Boxalino\DataIntegration\Model\Indexer\Product;

use Boxalino\DataIntegrationDoc\Framework\Integrate\Type\ProductTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\ProductDeltaIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Delta
 * Delta Data Sync : exports to DI the products updated within a configurable time frame
 *
 * @package Boxalino\Exporter\Model\Indexer
 */
class Delta extends \Boxalino\DataIntegration\Model\Indexer\Delta
    implements \Magento\Framework\Indexer\ActionInterface,
    \Magento\Framework\Mview\ActionInterface
{
    use ProductTrait;

    /**
     * Exporter ID in configuration
     */
    const INDEXER_ID = 'boxalino_di_delta_product';

    /**
     * DI type
     */
    const INDEXER_TYPE = 'di_delta_product';

    /**
     * @var ProductDeltaIntegrationHandlerInterface
     */
    protected $integrationHandler;

    public function __construct(
        LoggerInterface $logger,
        DiConfigurationInterface $configurationManager,
        ProductDeltaIntegrationHandlerInterface $integrationHandler
    ){
        $this->integrationHandler = $integrationHandler;
        parent::__construct($logger, $configurationManager);
    }

}
