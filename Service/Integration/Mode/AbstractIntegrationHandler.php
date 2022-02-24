<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration\Mode;

use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Framework\Integrate\DiLoggerTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\IntegrationHandler;

/**
 * @package Boxalino\DataIntegration\Service\Integration\Mode
 */
abstract class AbstractIntegrationHandler extends IntegrationHandler
    implements DiIntegrationConfigurationInterface
{
    use DiIntegrationConfigurationTrait;
    use DiLoggerTrait;

    public function __construct(
        LoggerInterface $logger,
        array $docHandlers = [],
        int $timeout = 0
    ){
        parent::__construct();
        $this->logger = $logger;
        foreach($docHandlers as $doc => $resolver)
        {
            if($resolver instanceof DocHandlerInterface)
            {
                $this->addHandler($resolver);
            }
        }
        $this->setTimeout($timeout);
    }

    /**
     * @return string
     */
    abstract public function getEntityName() : string;


}
