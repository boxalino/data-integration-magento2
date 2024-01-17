<?php
namespace Boxalino\DataIntegration\Model\Indexer\Content;

use Boxalino\DataIntegrationDoc\Framework\Integrate\DiIntegrateTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\DiLoggerTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Mode\Configuration\DeltaTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Mode\Configuration\InstantTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Type\ContentTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\ContentDeltaIntegrationHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Util\ConfigurationDataObject;
use Psr\Log\LoggerInterface;

/**
 * Class Delta
 * Delta Data Sync : exports to DI the content updated within a configurable time frame
 *
 * @package Boxalino\Exporter\Model\Indexer
 */
class Delta extends \Boxalino\DataIntegration\Model\Indexer\Delta
{
    use ContentTrait;

    /**
     * Exporter ID in configuration
     */
    const INDEXER_ID = 'boxalino_di_delta_content';

    /**
     * DI type
     */
    const INDEXER_TYPE = 'di_delta_content';

    /**
     * @var ContentDeltaIntegrationHandlerInterface 
     */
    protected $integrationHandler;

    public function __construct(
        LoggerInterface $logger,
        DiConfigurationInterface $configurationManager,
        ContentDeltaIntegrationHandlerInterface $integrationHandler
    ){
        $this->integrationHandler = $integrationHandler;
        parent::__construct($logger, $configurationManager);
    }


}
