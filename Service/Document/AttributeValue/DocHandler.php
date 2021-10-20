<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\AttributeValue;

use Boxalino\DataIntegrationDoc\Doc\AttributeValue;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DocGeneratorInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocAttributeValuesHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocAttributeValues;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;
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
        if($this->getSystemConfiguration()->isTest())
        {
            $this->getLogger()->info("Boxalino DI: load for {$this->getDocType()}");
        }

        $this->createDocLines();
        parent::integrate();
    }

    /**
     * @return $this
     */
    protected function createDocLines() : self
    {
        $this->addSystemConfigurationOnHandlers();
        try {
            foreach($this->getHandlers() as $handler)
            {
                if($handler instanceof DocSchemaPropertyHandlerInterface)
                {
                    $this->_createDocLinesByHandler($handler);
                }
            }
        } catch (\Throwable $exception)
        {
            $this->logger->info($exception->getMessage());
        }

        return $this;
    }

    /**
     * The doc_attribute_values are grouped by the property-name as to avoid overlap of
     * @param DocSchemaPropertyHandlerInterface $handler
     */
    protected function _createDocLinesByHandler(DocSchemaPropertyHandlerInterface $handler) : void
    {
        /** @var array: [property-name => [$schema, $schema], property-name => [], [..]] $data */
        $data = $handler->getValues();
        foreach($data as $propertyName => $content)
        {
            foreach($content as $schema)
            {
                /** @var AttributeValue | DocGeneratorInterface $doc */
                $doc = $this->getDocSchemaGenerator($schema);
                $doc->setCreationTm(date("Y-m-d H:i:s"));

                $this->addDocLine($doc);
            }
        }
    }


}
