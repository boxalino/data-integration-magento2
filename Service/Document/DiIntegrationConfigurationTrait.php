<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Util\ConfigurationDataObject;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Trait DiIntegrationConfigurationTrait
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
trait DiIntegrationConfigurationTrait
{

    /**
     * @var ConfigurationDataObject
     */
    protected $systemConfiguration;

    /**
     * @var string
     */
    protected $handlerIntegrateTime;

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
     * @return string
     */
    public function getHandlerIntegrateTime(): string
    {
        if(!$this->handlerIntegrateTime)
        {
            $this->setHandlerIntegrateTime((new \DateTime())->format("Y-m-d H:i:s"));
        }

        return $this->handlerIntegrateTime;
    }

    /**
     * @param string $handlerIntegrateTime
     */
    public function setHandlerIntegrateTime(string $handlerIntegrateTime) : void
    {
        $this->handlerIntegrateTime = $handlerIntegrateTime;
    }

    /**
     * setIds and setSystemConfiguration to all of the Attribute elements
     * for data access purposes
     */
    public function addSystemConfigurationOnHandlers()
    {
        foreach($this->getHandlers() as $handler)
        {
            if($handler instanceof DiIntegrationConfigurationInterface)
            {
                $handler->setSystemConfiguration($this->getSystemConfiguration());
                $handler->setHandlerIntegrateTime($this->getHandlerIntegrateTime());
            }

            try{
                if($handler instanceof DocDeltaIntegrationInterface)
                {
                    if($handler->filterByCriteria())
                    {
                        if(count($this->getIds()))
                        {
                            if($handler instanceof DocMviewDeltaIntegrationInterface)
                            {
                                $handler->setMviewIds($this->getIds());
                                continue;
                            }
                        }

                        $handler->setSyncCheck($this->getSyncCheck());
                    }
                }
            } catch (\Throwable $exception) {}

            try{
                if($handler instanceof DocInstantIntegrationInterface)
                {
                    if($handler->filterByIds())
                    {
                        $handler->setIds($this->getIds());
                    }
                }
            } catch (\Throwable $exception) {}
        }
    }

    /**
     * @return string
     */
    public function getDiIdField() : string
    {
        return DocSchemaInterface::DI_ID_FIELD;
    }

    /**
     * @return int
     */
    public function getDataOffset() : int
    {
        return (int)$this->getSystemConfiguration()->getBatchSize()*$this->getSystemConfiguration()->getChunk();
    }

    /**
     * @return ConfigurationDataObject
     */
    public function getDiConfiguration() : ConfigurationDataObject
    {
        return $this->getSystemConfiguration();
    }


}
