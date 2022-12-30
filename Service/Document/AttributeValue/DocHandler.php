<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\AttributeValue;

use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocAttributeValuesHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocAttributeValues;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\DeltaIntegrationInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DocHandler
 *
 * Generator for the doc_attribute_value document
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252313624/doc+attribute+values
 *
 * The doc_attribute_value is exported fully for FULL and DELTA data integrations
 * The doc_attribute_value is exported partially for INSTANT data integrations (ex: only categories - based on available configurations)
 *
 * @package Boxalino\DataIntegration\Service\Document\Attribute\Value
 */
class DocHandler extends DocAttributeValues implements
    DocAttributeValuesHandlerInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface
{
    use DiIntegrationConfigurationTrait;

    public function __construct(
        LoggerInterface $logger,
        array $propertyHandlers = []
    ){
        parent::__construct($logger);
        foreach($propertyHandlers as $key => $propertyHandler)
        {
            if($propertyHandler instanceof DocSchemaPropertyHandlerInterface)
            {
                $this->addPropertyHandler($propertyHandler);
            }
        }
    }

    /**
     * Integrate document
     */
    public function integrate() : void
    {
        if($this->getSystemConfiguration()->getMode()=== DeltaIntegrationInterface::INTEGRATION_MODE)
        {
            if($this->getSystemConfiguration()->getOutsource())
            {
                $this->logInfo("load for {$this->getDocType()} is outsourced.");
                return;
            }
        }

        $this->logInfo("load for {$this->getDocType()}");

        $this->createDocLines();
        parent::integrate();
    }

    /**
     * @return void
     */
    protected function createDocLines() : void
    {
        $this->logTime("start" . __FUNCTION__);
        $this->addSystemConfigurationOnHandlers();
        try {
            foreach($this->getHandlers() as $handler)
            {
                $this->logMemory(get_class($handler));
                $this->logTime("startTimeHandler");

                if($handler instanceof DocSchemaPropertyHandlerInterface)
                {
                    $this->_createDocLinesByHandler($handler);
                }

                $this->logTime("endTimeHandler");
                $this->logMemory(get_class($handler), false);
                $this->logMessage(get_class($handler), "endTimeHandler", "startTimeHandler");
            }
        } catch (\Throwable $exception)
        {
            $this->logger->info($exception->getMessage());
        }

        $this->logTime("end" . __FUNCTION__);
        $this->logMessage(__FUNCTION__, "end" . __FUNCTION__, "start" . __FUNCTION__);
    }

    /**
     * The doc_attribute_values are grouped by the property-name as to avoid overlap of
     * @param DocSchemaPropertyHandlerInterface $handler
     */
    protected function _createDocLinesByHandler(DocSchemaPropertyHandlerInterface $handler) : void
    {
        /** @var array: [property-name => [$schema, $schema], property-name => [], [..]] $data */
        foreach($handler->getValues() as $propertyName => $content)
        {
            foreach($content as $schema)
            {
                $this->addDocLine(
                    $this->getDocSchemaGenerator($schema)
                        ->setCreationTm(date("Y-m-d H:i:s"))
                );
            }

            unset($content);
        }
    }


}
