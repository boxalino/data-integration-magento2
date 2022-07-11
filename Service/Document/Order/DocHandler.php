<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrateTrait;
use Boxalino\DataIntegration\Service\Document\DocMviewDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Doc\Order;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DocGeneratorInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocOrderHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocOrder;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\FullIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\InstantIntegrationTrait;
use Psr\Log\LoggerInterface;

/**
 * Class DocHandler
 * Generates the content for the doc_order document
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252313666/doc+order
 *
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
class DocHandler extends DocOrder implements
    DocOrderHandlerInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface,
    DocDeltaIntegrationInterface,
    DocMviewDeltaIntegrationInterface
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
     * @return $this
     */
    protected function createDocLines() : self
    {
        try {
            $this->addSystemConfigurationOnHandlers();
            $this->generateDocData();

            foreach($this->getDocData() as $id=>$content)
            {
                /** @var Order | DocHandlerInterface | DocGeneratorInterface $doc */
                $doc = $this->getDocSchemaGenerator($content);
                $doc->setCreationTm(date("Y-m-d H:i:s"));

                try{
                    if($doc->getInternalId())
                    {
                        $this->addDocLine($doc);
                    }
                } catch (\Throwable $exception)
                {
                    if($this->getSystemConfiguration()->isTest())
                    {
                        $this->logger->warning("Incomplete content for order: " . $doc->jsonSerialize());
                    }

                    continue;
                }
            }

            $this->addSeekConditionToBatch();
            $this->resetDocData();
        } catch (\Throwable $exception)
        {
            $this->logger->info($exception->getMessage());
        }

        return $this;
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
        /** @var Order | DocHandlerInterface | DocGeneratorInterface | null $lastRecord */
        $lastRecord = $this->getLastDoc();
        if(is_null($lastRecord))
        {
            return;
        }

        $this->getSystemConfiguration()->setChunk((string) $lastRecord->getInternalId());
    }


}
