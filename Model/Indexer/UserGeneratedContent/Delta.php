<?php
namespace Boxalino\DataIntegration\Model\Indexer\UserGeneratedContent;

use Boxalino\DataIntegrationDoc\Framework\Integrate\DiIntegrateTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\DiLoggerTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Mode\Configuration\DeltaTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Mode\Configuration\InstantTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\Type\UserGeneratedContentTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\UserGeneratedContentDeltaIntegrationHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Util\ConfigurationDataObject;
use Psr\Log\LoggerInterface;

/**
 * Class Delta
 * Delta Data Sync : exports to DI the user generated content updated within a configurable time frame
 *
 * @package Boxalino\Exporter\Model\Indexer
 */
class Delta extends \Boxalino\DataIntegration\Model\Indexer\Delta
{
    use UserGeneratedContentTrait;

    /**
     * Exporter ID in configuration
     */
    const INDEXER_ID = 'boxalino_di_delta_user_generated_content';

    /**
     * DI type
     */
    const INDEXER_TYPE = 'di_delta_user_generated_content';

    /**
     * @var UserGeneratedContentDeltaIntegrationHandlerInterface 
     */
    protected $integrationHandler;

    public function __construct(
        LoggerInterface $logger,
        DiConfigurationInterface $configurationManager,
        UserGeneratedContentDeltaIntegrationHandlerInterface $integrationHandler
    ){
        $this->integrationHandler = $integrationHandler;
        parent::__construct($logger, $configurationManager);
    }


}
