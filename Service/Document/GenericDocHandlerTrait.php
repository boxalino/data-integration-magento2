<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegration\Service\Document\DiIntegrateTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\NoRecordsFoundException;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;
use Psr\Log\LoggerInterface;

/**
 * Generic flow for creating a JSONL document
 * It is used for doc_content, doc_user_selection, doc_user_generated_content, doc_bundle, doc_voucher data types
 * Each of the property handlers defined via di.xml can be of type GenericDocLineHandler
 * When a property handler returns a NoRecordsFoundException - it goes to the next property handler
 *
 * NOTE: instant data syncs are not required with these documents
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
trait GenericDocHandlerTrait
{
    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DiIntegrateTrait;
    use DocMviewDeltaIntegrationTrait;
    use DocInstantIntegrationTrait;

    /**
     * @param LoggerInterface $logger
     * @param array $propertyHandlers
     */
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
     * Loop through all different content models and create the JSONL content
     * (in-memory)
     * @return void
     */
    protected function createDocLines() : void
    {
        $this->logTime("start" . __FUNCTION__);
        $this->addSystemConfigurationOnHandlers();
        try {
            foreach($this->getHandlers() as $handler)
            {
                $this->handlerLogMemory($handler);
                $this->logTime("startTimeHandler");

                if($handler instanceof DocSchemaPropertyHandlerInterface)
                {
                    $this->_createDocLinesByHandler($handler);
                }

                $this->logTime("endTimeHandler");
                $this->handlerLogMemory($handler, false);
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
     * @param $handler
     * @param bool $start
     * @return void
     */
    protected function handlerLogMemory($handler, bool $start = true)
    {
        if($handler instanceof GenericDocLineHandler)
        {
            $this->logMemory($handler->getResolverType(), $start);
            return;
        }

        $this->logMemory(get_class($handler), $start);
    }

    /**
     * Each of the property handler returns rows with already-computed document data structure
     *
     * @param DocSchemaPropertyHandlerInterface $handler
     * @return void
     */
    protected function _createDocLinesByHandler(DocSchemaPropertyHandlerInterface $handler) : void
    {
        try{
            /** @var array: [$resourceId1 => $schema1, $resourceId2 => $schema2,.. ] $data */
            foreach($handler->getValues() as $schema)
            {
                $this->addDocLine(
                    $this->getDocSchemaGenerator($schema)->setCreationTm(date("Y-m-d H:i:s"))
                );

                unset($schema);
            }
        } catch (NoRecordsFoundException $exception)
        {
            $this->logInfo($exception->getMessage());
        }

    }

    /**
     * All data is loaded at once
     * (it is not expected to use Magento2 as a CMS)
     *
     * @return bool
     */
    public function chunk() : bool
    {
        return false;
    }


}
