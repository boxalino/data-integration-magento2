<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegrationDoc\Service\ErrorHandler\NoRecordsFoundException;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\InstantIntegrationInterface;
use Psr\Log\LoggerInterface;

/**
 * Trait DiIntegrateTrait
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
trait DiIntegrateTrait
{

    use DiIntegrationConfigurationTrait;

    /**
     * The major items content is integrated in batches
     * due to the big amount of content required for the export
     */
    public function integrate(): void
    {
        try{
            $this->createDocLines();
        } catch (NoRecordsFoundException $exception)
        {
            //logical exception to break the loop
            //reset the docs in case the attributeHandlers were not run in the random order
            $this->resetDocs();
        } catch (\Throwable $exception)
        {
            throw $exception;
        }

        /** for instant data integrations - the generic load is sufficient */
        if($this->getSystemConfiguration()->getMode() == InstantIntegrationInterface::INTEGRATION_MODE)
        {
            parent::integrate();
            if($this->getSystemConfiguration()->isTest())
            {
                $this->getLogger()->info("Boxalino DI: load for {$this->getDocType()}");
            }
            return;
        }

        if(count($this->docs))
        {
            if($this->chunk())
            {
                $this->integrateByChunk();
                return;
            }

            $this->integrateFull();
            return;
        }

        if($this->getSystemConfiguration()->getChunk())
        {
            $this->loadBq();
            if($this->getSystemConfiguration()->isTest())
            {
                $this->getLogger()->info("Boxalino DI: load for {$this->getDocType()}");
            }
            return;
        }

        throw new NoRecordsFoundException("No {$this->getDocType()} content viable for sync since " . $this->getSyncCheck());
    }

    /**
     * @return void
     */
    public function integrateFull() : void
    {
        if($this->getSystemConfiguration()->isTest())
        {
            $this->getLogger()->info("Boxalino DI: creating the document JSONL from structured DB load.");
        }
        $document = $this->getDocContent();
        $this->loadByChunk($document);
        unset($document);

        $this->loadBq();
        if($this->getSystemConfiguration()->isTest())
        {
            $this->getLogger()->info("Boxalino DI: load for {$this->getDocType()}");
        }
    }

    /**
     * Synchronize content based on the batch size
     */
    public function integrateByChunk()
    {
        if($this->getSystemConfiguration()->isTest())
        {
            $this->getLogger()->info("Boxalino DI: creating the document JSONL from structured DB load.");
        }
        $document = $this->getDocContent();
        $this->loadByChunk($document);
        unset($document);

        $this->integrate();
    }


    /** these functions are already included in any DocHandler element */
    abstract public function getDocContent() : string;
    abstract public function loadByChunk(string $document) : void;
    abstract public function chunk() : bool;
    abstract public function getLogger() : LoggerInterface;
    abstract public function loadBq() : void;
    abstract public function getDocType() : string;
    abstract public function getSyncCheck() : ?string;
    abstract public function resetDocs() : void;
    abstract public function createDocLines() : void;


}
