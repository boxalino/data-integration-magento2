<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegrationDoc\Service\Util\ConfigurationDataObject;

/**
 * Trait DiSystemConfigurationTrait
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
trait DiSystemConfigurationTrait
{

    /**
     * @var ConfigurationDataObject
     */
    protected $systemConfiguration;

    /**
     * @return ConfigurationDataObject
     */
    public function getSystemConfiguration(): ConfigurationDataObject
    {
        return $this->systemConfiguration;
    }

    /**
     * @param ConfigurationDataObject $configuration
     * @return void
     */
    public function setSystemConfiguration(ConfigurationDataObject $configuration): void
    {
        $this->systemConfiguration = $configuration;
    }

    /**
     * @return ConfigurationDataObject
     */
    public function getDiConfiguration() : ConfigurationDataObject
    {
        return $this->getSystemConfiguration();
    }


}
