<?php
namespace Boxalino\DataIntegration\Model\Indexer\UserSelection;

use Boxalino\DataIntegrationDoc\Framework\Integrate\DiIntegrateTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\DiLoggerTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Mode\Configuration\DeltaTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Mode\Configuration\InstantTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Type\UserSelectionTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\UserSelectionDeltaIntegrationHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Util\ConfigurationDataObject;
use Psr\Log\LoggerInterface;

/**
 * Class Delta
 * Delta Data Sync : exports to DI the user_selection updated within a configurable time frame
 *
 * @package Boxalino\Exporter\Model\Indexer
 */
class Delta extends \Boxalino\DataIntegration\Model\Indexer\Delta
{
    use UserSelectionTrait;

    /**
     * Exporter ID in configuration
     */
    const INDEXER_ID = 'boxalino_di_delta_user_selection';

    /**
     * DI type
     */
    const INDEXER_TYPE = 'di_delta_user_selection';

    /**
     * @var UserSelectionDeltaIntegrationHandlerInterface 
     */
    protected $integrationHandler;

    public function __construct(
        LoggerInterface $logger,
        DiConfigurationInterface $configurationManager,
        UserSelectionDeltaIntegrationHandlerInterface $integrationHandler
    ){
        $this->integrationHandler = $integrationHandler;
        parent::__construct($logger, $configurationManager);
    }


}
