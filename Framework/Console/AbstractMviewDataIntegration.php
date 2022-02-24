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
 * Class AbstractMviewDataIntegration
 *
 * Used as a CLI command
 *
 * Has been re-defined in the magento2-module in order to allow the use of a custom log handler
 * If the integration deltas are handled by MVIEW - use this event in order to clear the mview after a succesfull sync
 */
abstract class AbstractMviewDataIntegration extends DiGenericAbstractCommand
{

    /**
     * @var string | null
     */
    protected $mviewViewId;

    /**
     * @var string | null
     */
    protected $mviewGroupId;

    /**
     * @var DiViewHandlerInterface
     */
    protected $mviewViewHandler;

    /**
     * @var int | null
     */
    protected $lastVersionId = null;

    /**
     * @var int | null
     */
    protected $currentVersionId = null;

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
        $account = $input->getArgument("account");
        $output->writeln('Start of Boxalino Data Integration (DI) ...');

        try{
            $this->_reviewMview();

            $exceptionMessages = $this->_execute($account);

            if(empty($exceptionMessages))
            {
                if(!is_null($this->lastVersionId) && !is_null($this->currentVersionId))
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
     * @param string|null $account
     * @return array
     */
    abstract protected function _execute(?string $account = null) : array;

    /**
     * @return void
     */
    protected function _reviewMview() : void
    {
        try{
            if($this->mviewViewId)
            {
                $this->lastVersionId = $this->mviewViewHandler->getLastVersionIdByViewId($this->mviewViewId, $this->mviewGroupId);
                $this->currentVersionId = $this->mviewViewHandler->getCurrentVersionIdByViewId($this->mviewViewId, $this->mviewGroupId);

                $this->getLogger()->info("Boxalino DI: Mview information: " .
                    json_encode([
                        "group" => $this->mviewGroupId,
                        "viewId" => $this->mviewViewId,
                        "currentVersionId"=> $this->currentVersionId,
                        "lastVersionId"=> $this->lastVersionId,
                        "backlog" => $this->mviewViewHandler->getBacklogSizeByViewId($this->mviewViewId, $this->lastVersionId, $this->currentVersionId)
                    ]));
            }
        } catch (\Throwable $exception)
        {
            $this->lastVersionId = null; $this->currentVersionId = null;
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
            $this->getLogger()->info("Boxalino DI: Updating the mview version for $this->mviewGroupId:$this->mviewViewId from $this->currentVersionId to $this->lastVersionId");
            $this->mviewViewHandler->updateVersionIdByViewId($this->mviewViewId, $this->lastVersionId, $this->mviewGroupId);

            $this->getLogger()->info("Boxalino DI: Clearing the $this->mviewGroupId:$this->mviewViewId  changelog for version older than $this->lastVersionId");
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
                $this->getMviewViewId(), $this->getLastVersionId(), $this->getCurrentVersionId(), $this->getMviewGroupId()
        );
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
    public function getLastVersionId(): ?int
    {
        return $this->lastVersionId;
    }

    /**
     * @return int|null
     */
    public function getCurrentVersionId(): ?int
    {
        return $this->currentVersionId;
    }


}
