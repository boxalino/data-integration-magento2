<?php
namespace Boxalino\DataIntegration\Api\Mview;

use Boxalino\DataIntegration\Service\ErrorHandler\MviewViewIdNotFoundException;
use Magento\Framework\Mview\View\ChangelogTableNotExistsException;

/**
 * Interface DiViewHandlerInterface
 * Generic helping functions to assist in managing Magento2 MVIEW Processor / View / Changelog
 */
interface DiViewHandlerInterface
{

    /**
     * @param array $mviewIds
     * @param string $websiteId
     * @param string $viewId
     * @param string|null $group
     * @return array
     */
    public function getBacklogForWebsiteId(array $mviewIds, string $websiteId, string $viewId, ?string $group = null) : array;

    /**
     * @param string $viewId
     * @param int $fromVersion
     * @param int $toVersion
     * @param string|null $group
     * @return int[]
     * @throws MviewViewIdNotFoundException
     */
    public function getBacklogByViewId(string $viewId, int $fromVersion, int $toVersion, ?string $group = null) : array;

    /**
     * @param string $viewId
     * @param int $fromVersion
     * @param int $toVersion
     * @param string|null $group
     * @return int[]
     * @throws MviewViewIdNotFoundException
     */
    public function getAffectedBacklogByViewId(string $viewId, int $fromVersion, int $toVersion, ?string $group = null) : array;

    /**
     * @param string $viewId
     * @param int $fromVersion
     * @param int $toVersion
     * @param string|null $group
     * @return int
     * @throws MviewViewIdNotFoundException
     */
    public function getBacklogSizeByViewId(string $viewId, int $fromVersion, int $toVersion, ?string $group = null) : int;

    /**
     * @param string $viewId
     * @param string $versionId
     * @param string|null $group
     * @return string
     * @throws MviewViewIdNotFoundException
     */
    public function updateVersionIdByViewId(string $viewId, int $versionId, ?string $group = null) : void;

    /**
     * @param string $viewId
     * @param string|null $group
     * @return int
     * @throws ChangelogTableNotExistsException | MviewViewIdNotFoundException
     */
    public function getChangelogVersionIdByViewId(string $viewId, ?string $group = null) : int;

    /**
     * @param string $viewId
     * @param string|null $group
     * @return int
     * @throws MviewViewIdNotFoundException
     */
    public function getMviewVersionIdByViewId(string $viewId, ?string $group = null) : int;

    /**
     * @param string $viewId
     * @param string|null $group
     * @return void
     * @throws MviewViewIdNotFoundException
     */
    public function clearChangelogByViewId(string $viewId, ?string $group = null) : void;


}
