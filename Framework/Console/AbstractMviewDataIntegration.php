<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Framework\Console;

use Boxalino\DataIntegration\Api\Mview\DiViewHandlerInterface;
use Boxalino\DataIntegration\Service\ErrorHandler\EmptyBacklogException;
use Boxalino\DataIntegrationDoc\Framework\Console\DiGenericAbstractCommand;
use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Magento\Framework\Mview\View\ChangelogTableNotExistsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractFullMviewDataIntegration
 *
 * Used as a CLI command
 *
 * Has been re-defined in the magento2-module in order to allow the use of a custom log handler
 * If the integration deltas are handled by MVIEW - use this event in order to clear the mview after a succesfull sync
 */
abstract class AbstractMviewDataIntegration extends DiGenericAbstractCommand
{

    /**
     * @var strin | null
     */
    protected $mviewViewId;

    /**
     * @var strin | null
     */
    protected $mviewGroupId;

    /**
     * @var DiViewHandlerInterface
     */
    protected $mviewViewHandler;

    /**
     * @var int | null
     */
    protected $mviewVersionId = null;

    /**
     * @var int | null
     */
    protected $changelogVersionId = null;

    public function __construct(
        DiViewHandlerInterface $diViewHandler,
        LoggerInterface $logger,
        DiConfigurationInterface $configurationManager,
        string $mviewViewId,
        ?string $mviewGroupId = "boxalino_di"
    ){
        parent::__construct($logger, $configurationManager);
        $this->mviewViewHandler = $diViewHandler;
        $this->mviewViewId = $mviewViewId;
        $this->mviewGroupId = $mviewGroupId;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Start of Boxalino Data Integration (DI)...");
        try{
            $this->_reviewMview();

            $exceptionMessages = $this->_execute();

            if(empty($exceptionMessages))
            {
                if(!is_null($this->mviewVersionId) && !is_null($this->changelogVersionId))
                {
                    $this->_updateMview();
                }

                $output->writeln("End of Boxalino Data Integration Process.");
                return 0;
            }

            $output->writeln(json_encode($exceptionMessages));
        } catch (EmptyBacklogException $exception)
        {
            $this->getLogger()->info($exception->getMessage());
            $output->writeln($exception->getMessage());
            return 0;
        } catch (\Throwable $exception)
        {
            $this->getLogger()->alert($exception->getMessage());
            $output->writeln($exception->getMessage());
            return 0;
        }
    }

    /**
     * @return array
     */
    public function getInputList(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getUsages() : array
    {
        return [
            "Data sync to Boxalino GCP for Magento2 integrations using the MVIEW indexer mode.",
            "After the content is successfully exported for all configured accounts - the MVIEW state id is updated and the MVIEW backlog is cleared."
        ];
    }

    /**
     * The execute strategy is different for Delta / Full / Instant modes
     * Review the available traits for further description
     *
     * @return array
     */
    abstract protected function _execute() : array;

    /**
     * @return void
     */
    protected function _reviewMview() : void
    {
        try{
            if($this->mviewViewId)
            {
                $this->mviewVersionId = $this->mviewViewHandler->getMviewVersionIdByViewId($this->mviewViewId, $this->mviewGroupId);
                $this->changelogVersionId = $this->mviewViewHandler->getChangelogVersionIdByViewId($this->mviewViewId, $this->mviewGroupId);

                $this->getLogger()->info("Boxalino DI: Mview information: " .
                    json_encode([
                        "group" => $this->mviewGroupId,
                        "viewId" => $this->mviewViewId,
                        "changelogVersionId"=> $this->changelogVersionId,
                        "mviewVersionId"=> $this->mviewVersionId,
                        "backlog" => $this->mviewViewHandler->getBacklogSizeByViewId($this->mviewViewId, $this->mviewVersionId, $this->changelogVersionId),
                        "affected" => count($this->mviewViewHandler->getBacklogByViewId($this->mviewViewId, $this->mviewVersionId, $this->changelogVersionId))
                    ]));
            }
        } catch (\Throwable $exception)
        {
            $this->mviewVersionId = null; $this->changelogVersionId = null;
            $this->getLogger()->warning($exception->getMessage());
        }
    }

    /**
     * @return void
     * @throws \Throwable
     */
    protected function _updateMview() : void
    {
        try{
            $this->getLogger()->info("Boxalino DI: Updating the mview version for $this->mviewGroupId:$this->mviewViewId from $this->mviewVersionId to $this->changelogVersionId");
            $this->mviewViewHandler->updateVersionIdByViewId($this->mviewViewId, $this->changelogVersionId, $this->mviewGroupId);

            $this->getLogger()->info("Boxalino DI: Clearing the $this->mviewGroupId:$this->mviewViewId  changelog for version older than $this->changelogVersionId");
            $this->mviewViewHandler->clearChangelogByViewId($this->mviewViewId, $this->mviewGroupId);
        } catch (\Throwable $exception)
        {
            $exception = new \Exception("Boxalino DI: Failed to update the mview for $this->mviewGroupId:$this->mviewViewId:" . $exception->getMessage());
            $this->logOrThrowException($exception);
        }
    }

    /**
     * @return array
     */
    public function getMviewIds() : array
    {
        return $this->getMviewViewHandler()->getBacklogByViewId(
            $this->getMviewViewId(), $this->getMviewVersionId(), $this->getChangelogVersionId(), $this->getMviewGroupId()
        );
    }

    /**
     * @param array $mviewIds
     * @param string $websiteId
     * @return array
     */
    public function getAffectedIdsByWebsite(array $mviewIds, string $websiteId) : array
    {
        return $this->getMviewViewHandler()->getBacklogForWebsiteId($mviewIds, $websiteId, $this->getMviewViewId(), $this->getMviewGroupId());
    }

    /**
     * @return DiViewHandlerInterface
     */
    public function getMviewViewHandler() : DiViewHandlerInterface
    {
        return $this->mviewViewHandler;
    }

    /**
     * @return string
     */
    public function getMviewViewId() : string
    {
        return $this->mviewViewId;
    }

    /**
     * @return string|null
     */
    public function getMviewGroupId() : string
    {
        return $this->mviewGroupId;
    }

    /**
     * @return int|null
     */
    public function getMviewVersionId(): ?int
    {
        return $this->mviewVersionId;
    }

    /**
     * @return int|null
     */
    public function getChangelogVersionId(): ?int
    {
        return $this->changelogVersionId;
    }


}
