<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Attribute;

use Boxalino\DataIntegrationDoc\Doc\Attribute;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DocGeneratorInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocAttribute;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocAttributeHandlerInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\DeltaIntegrationInterface;
use Psr\Log\LoggerInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;

/**
 * Class DocHandler
 * Generator for the doc_attribute document
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280945/doc+attribute
 *
 * The doc_attribute is exported only for FULL and INSTANT data integrations
 *
 * @package Boxalino\DataIntegration\Service\Document\Attribute
 */
class DocHandler extends DocAttribute
    implements DocAttributeHandlerInterface, DiIntegrationConfigurationInterface
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

    public function integrate(): void
    {
        if($this->getSystemConfiguration()->getMode()=== DeltaIntegrationInterface::INTEGRATION_MODE)
        {
            if($this->getSystemConfiguration()->getOutsource())
            {
                if($this->getSystemConfiguration()->isTest())
                {
                    $this->getLogger()->info("Boxalino DI: load for {$this->getDocType()} is outsourced.");
                }

                return;
            }
        }

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

            /**
             * other properties outside of product table & properties
             */
            $this->addConfiguredProperties();
            
        } catch (\Throwable $exception)
        {
            $this->getLogger()->info($exception->getMessage());
        }

        return $this;
    }

    /**
     * @param DocSchemaPropertyHandlerInterface $handler
     */
    protected function _createDocLinesByHandler(DocSchemaPropertyHandlerInterface $handler) : void
    {
        /** @var array: [property-name => [$schema, $schema], property-name => [], [..]] $data */
        $data = $handler->getValues();
        foreach($data as $propertyName => $content)
        {
            if(in_array($propertyName, $this->getExcludedProperties()))
            {
                continue;
            }

            /** @var Attribute | DocGeneratorInterface $doc */
            $doc = $this->getDocSchemaGenerator($content);
            $doc->setCreationTm(date("Y-m-d H:i:s"));

            $this->addDocLine($doc);
        }
    }

    /**
     * @return string[]
     */
    public function getExcludedProperties() : array
    {
        return [
            "id", "title", "body", "categories", "tags", "categories_text", "scorerTerms", "addedTime", "changedTime", "price",
            "standardPrice", "discountedPrice", "stockCounter", "viewCounter", "purchaseCounter", "random_*", "sortable_title"
        ];
    }


}
