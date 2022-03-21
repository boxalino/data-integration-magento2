<?php
namespace Boxalino\DataIntegration\Model\Indexer\Mview;

use Boxalino\DataIntegration\Api\Mview\DiViewHandlerInterface;
use Boxalino\DataIntegration\Api\Mview\DiViewIdResourceInterface;
use Boxalino\DataIntegration\Service\ErrorHandler\MviewViewIdNotFoundException;
use Magento\Framework\Mview\View\ChangelogTableNotExistsException;
use Magento\Framework\Mview\View\CollectionFactory;
use Magento\Framework\Mview\ViewInterface;

/**
 * Class View
 * Assists in managing the mview view states
 *
 * Set the $mviewGroupId argument for matching the group configured in the DI Integration Layer
 *
 * @package Boxalino\DataIntegration\Model\Indexer\Mview
 */
class View implements DiViewHandlerInterface
{

    /**
     * @var CollectionFactory
     */
    protected $viewsFactory;

    /**
     * @var string
     */
    protected $mviewGroupId;

    /**
     * @var \ArrayObject
     */
    protected $mviewGroupCollections;

    /**
     * @var \ArrayObject
     */
    protected $mviewViewIdsResourceCollection;

    /**
     * View constructor.
     */
    public function __construct(
        CollectionFactory $viewsFactory,
        array $mviewViewIdsResourceCollection = [],
        ?string $mviewGroupId = "boxalino_di"
    ){
        $this->viewsFactory = $viewsFactory;
        $this->mviewGroupId = $mviewGroupId;
        $this->mviewGroupCollections = new \ArrayObject();
        $this->mviewViewIdsResourceCollection = new \ArrayObject();
        foreach($mviewViewIdsResourceCollection as $viewId => $resourceObject)
        {
            $this->mviewViewIdsResourceCollection->offsetSet($viewId, $resourceObject);
        }
    }

    /**
     * @param array $mviewIds
     * @param string $websiteId
     * @param string $viewId
     * @param string|null $group
     * @return array
     */
    public function getBacklogForWebsiteId(array $mviewIds, string $websiteId, string $viewId, ?string $group = null) : array
    {
        $group = $group ?? $this->mviewGroupId;
        /** @var ViewInterface $view */
        foreach($this->getViewsByGroup($group) as $view)
        {
            if($view->getId() === $viewId)
            {
                $ids = $mviewIds;
                if($this->mviewViewIdsResourceCollection->offsetExists($viewId))
                {
                    /** @var DiViewIdResourceInterface $mviewViewIdsResource */
                    $mviewViewIdsResource = $this->mviewViewIdsResourceCollection->offsetGet($viewId);
                    $ids = $mviewViewIdsResource->getAffectedIdsByMviewIdsWebsiteId($ids, $websiteId);
                }

                return $ids;
            }
        }

        throw new MviewViewIdNotFoundException("The desired $viewId does not exist in mview group $group");
    }

    /**
     * @param string $viewId
     * @param int $fromVersion
     * @param int $toVersion
     * @param string|null $group
     * @return int[]
     * @throws MviewViewIdNotFoundException
     */
    public function getBacklogByViewId(string $viewId, int $fromVersion, int $toVersion, ?string $group = null) : array
    {
        $group = $group ?? $this->mviewGroupId;
        /** @var ViewInterface $view */
        foreach($this->getViewsByGroup($group) as $view)
        {
            if($view->getId() === $viewId)
            {
                $ids = $view->getChangelog()->getList($fromVersion, $toVersion);
                if($this->mviewViewIdsResourceCollection->offsetExists($viewId))
                {
                    /** @var DiViewIdResourceInterface $mviewViewIdsResource */
                    $mviewViewIdsResource = $this->mviewViewIdsResourceCollection->offsetGet($viewId);
                    $ids = $mviewViewIdsResource->getAffectedIdsByMviewIds($ids);
                }
                return $ids;
            }
        }

        throw new MviewViewIdNotFoundException("The desired $viewId does not exist in mview group $group");
    }

    /**
     * @param string $viewId
     * @param int $fromVersion
     * @param int $toVersion
     * @param string|null $group
     * @return int
     * @throws MviewViewIdNotFoundException
     */
    public function getBacklogSizeByViewId(string $viewId, int $fromVersion, int $toVersion, ?string $group = null) : int
    {
        $group = $group ?? $this->mviewGroupId;
        /** @var ViewInterface $view */
        foreach($this->getViewsByGroup($group) as $view)
        {
            if($view->getId() === $viewId)
            {
                $backlogSize = $view->getChangelog()->getList($fromVersion, $toVersion);
                return count($backlogSize);
            }
        }

        throw new MviewViewIdNotFoundException("The desired $viewId does not exist in mview group $group");
    }

    /**
     * @param string $viewId
     * @param int $versionId
     * @param string|null $group
     * @return void
     * @throws MviewViewIdNotFoundException
     */
    public function updateVersionIdByViewId(string $viewId, int $versionId, ?string $group = null) : void
    {
        $group = $group ?? $this->mviewGroupId;
        /** @var ViewInterface $view */
        foreach($this->getViewsByGroup($group) as $view)
        {
            if($view->getId() === $viewId)
            {
                $view->getState()->setVersionId($versionId)->save();
                return;
            }
        }

        throw new MviewViewIdNotFoundException("The desired $viewId does not exist in mview group $group");
    }

    /**
     * @param string $viewId
     * @param string|null $group
     * @return int
     * @throws ChangelogTableNotExistsException | MviewViewIdNotFoundException
     */
    public function getChangelogVersionIdByViewId(string $viewId, ?string $group = null) : int
    {
        $group = $group ?? $this->mviewGroupId;
        /** @var ViewInterface $view */
        foreach($this->getViewsByGroup($group) as $view)
        {
            if($view->getId() === $viewId)
            {
                try {
                    $currentVersionId = $view->getChangelog()->getVersion();
                } catch (ChangelogTableNotExistsException $exception) {
                    throw $exception;
                }

                return $currentVersionId;
            }
        }

        throw new MviewViewIdNotFoundException("The desired $viewId does not exist in mview group $group");
    }

    /**
     * @param string $viewId
     * @param string|null $group
     * @return int
     * @throws MviewViewIdNotFoundException
     */
    public function getMviewVersionIdByViewId(string $viewId, ?string $group = null) : int
    {
        $group = $group ?? $this->mviewGroupId;
        /** @var ViewInterface $view */
        foreach($this->getViewsByGroup($group) as $view)
        {
            if($view->getId() === $viewId)
            {
                return (int)$view->getState()->getVersionId();
            }
        }

        throw new MviewViewIdNotFoundException("The desired $viewId does not exist in mview group $group");
    }

    /**
     * @param string $viewId
     * @param string|null $group
     * @return void
     * @throws MviewViewIdNotFoundException
     */
    public function clearChangelogByViewId(string $viewId, ?string $group = null) : void
    {
        $group = $group ?? $this->mviewGroupId;
        foreach($this->getViewsByGroup($group) as $view)
        {
            if($view->getId() === $viewId)
            {
                $view->clearChangelog();
                return;
            }
        }

        throw new MviewViewIdNotFoundException("The desired $viewId does not exist in mview group $group");
    }

    /**
     * Return list of views by group
     *
     * @description as seen in Magento2 Magento\Framework\Mview\Processor.php
     * @param string $group
     * @return ViewInterface[]
     */
    protected function getViewsByGroup($group) : array
    {
        if($this->mviewGroupCollections->offsetExists($group))
        {
            return $this->mviewGroupCollections->offsetGet($group);
        }

        $collection = $this->viewsFactory->create();
        $views = $collection->getItemsByColumnValue('group', $group);
        $this->mviewGroupCollections->offsetSet($group, $views);

        return $views;
    }


}
