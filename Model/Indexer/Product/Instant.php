<?php
namespace Boxalino\DataIntegration\Model\Indexer\Product;

use Boxalino\DataIntegrationDoc\Framework\Integrate\Type\ProductTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\ProductInstantIntegrationHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Instant
 * Instant Data Sync : exports to DI the products updated within a configurable time frame
 *
 * @package Boxalino\Exporter\Model\Indexer
 */
class Instant extends \Boxalino\DataIntegration\Model\Indexer\Instant
    implements \Magento\Framework\Indexer\ActionInterface,
    \Magento\Framework\Mview\ActionInterface
{
    use ProductTrait;

    /**
     * Exporter ID in configuration
     */
    const INDEXER_ID = 'boxalino_di_instant_product';

    /**
     * DI type
     */
    const INDEXER_TYPE = 'di_instant_product';

    /**
     * @var ProductInstantIntegrationHandlerInterface
     */
    protected $integrationHandler;

    public function __construct(
        LoggerInterface $logger,
        DiConfigurationInterface $configurationManager,
        ProductInstantIntegrationHandlerInterface $integrationHandler
    ){
        $this->integrationHandler = $integrationHandler;
        parent::__construct($logger, $configurationManager);
    }

}
