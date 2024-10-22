<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Core;

use Boxalino\DataIntegration\Service\Document\DiSystemConfigurationTrait;
use Boxalino\DataIntegrationDoc\Service\Flow\DiLogTrait;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Core;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DataProviderInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DocHandler
 *
 * Baseline for exporting core data to Boxalino
 *
 * @package Boxalino\DataIntegration\Service\Document\UserSelection
 */
class DocHandler extends Core implements DiIntegrationConfigurationInterface
{

    use DiSystemConfigurationTrait;

    /**
     * Adding system configuration in case it is relevant to know which website is exported
     *
     * @return DataProviderInterface
     */
    protected function getDataProvider() : DataProviderInterface
    {
        if($this->dataProvider instanceof DiIntegrationConfigurationInterface)
        {
            $this->dataProvider->setSystemConfiguration($this->getSystemConfiguration());
        }

        return $this->dataProvider;
    }


}
