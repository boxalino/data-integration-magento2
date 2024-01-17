<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Doc\Order;
use Boxalino\DataIntegrationDoc\Doc\User;
use Boxalino\DataIntegrationDoc\Generator\DocGeneratorInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\FullIntegrationInterface;
use Psr\Log\LoggerInterface;

/**
 * Flow for creating a JSONL document for doc_user and doc_order
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
trait UserOrderDocHandlerTrait
{
    use DocInstantIntegrationTrait;
    use DocDeltaIntegrationTrait;
    use DocMviewDeltaIntegrationTrait;
    use DiIntegrateTrait;

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
     * @return void
     */
    protected function createDocLines() : void
    {
        try {
            $this->addSystemConfigurationOnHandlers();
            $this->generateDocData();

            foreach($this->getDocData() as $id => $content)
            {
                try{
                    if(isset($content[DocSchemaInterface::FIELD_INTERNAL_ID]))
                    {
                        $this->addDocLine(
                            $this->getDocSchemaGenerator($content)->setCreationTm(date("Y-m-d H:i:s"))
                        );
                    }
                } catch (\Throwable $exception)
                {
                    $this->logWarning("Incomplete content: " . json_encode($content));
                    continue;
                }
            }

            $this->addSeekConditionToBatch();
            $this->resetDocData();
        } catch (\Throwable $exception)
        {
            $this->logger->info($exception->getMessage());
        }
    }

    /**
     * @return bool
     */
    public function chunk() : bool
    {
        if($this->getSystemConfiguration()->getMode() == FullIntegrationInterface::INTEGRATION_MODE)
        {
            return true;
        }

        return false;
    }

    /**
     * The SEEK condition is set in order to avoid the use of OFFSET in SQL
     *
     * @return void
     */
    public function addSeekConditionToBatch() : void
    {
        /** @var Order | User | DocHandlerInterface | DocGeneratorInterface | null $lastRecord */
        $lastRecord = $this->getLastDoc();
        if(is_null($lastRecord))
        {
            return;
        }

        $this->getSystemConfiguration()->setChunk((string) $lastRecord->getInternalId());
    }

    
    
}
