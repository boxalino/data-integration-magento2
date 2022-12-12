<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\User;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrateTrait;
use Boxalino\DataIntegration\Service\Document\DocMviewDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Doc\User;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DocGeneratorInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUser;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocUserHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\FullIntegrationInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DocHandler
 * Generates the content for the doc_user document
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252182638/doc_user
 *
 * @package Boxalino\DataIntegration\Service\Document\User
 */
class DocHandler extends DocUser implements
    DocUserHandlerInterface,
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
     * @return void
     */
    protected function createDocLines() : void
    {
        try {
            $this->addSystemConfigurationOnHandlers();
            $this->generateDocData();

            foreach($this->getDocData() as $id=>$content)
            {
                /** @var User | DocHandlerInterface $doc */
                $doc = $this->getDocSchemaGenerator($content);
                $doc->setCreationTm(date("Y-m-d H:i:s"));

                try{
                    if($doc->getInternalId())
                    {
                        $this->addDocLine($doc);
                    }
                } catch (\Throwable $exception)
                {
                    $this->logWarning("Incomplete content for user: " . $doc->jsonSerialize());
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
        /** @var User | DocHandlerInterface | DocGeneratorInterface | null $lastRecord */
        $lastRecord = $this->getLastDoc();
        if(is_null($lastRecord))
        {
            return;
        }

        $this->getSystemConfiguration()->setChunk((string)$lastRecord->getInternalId());
    }



}
