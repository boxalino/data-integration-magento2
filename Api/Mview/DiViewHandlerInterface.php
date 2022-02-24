<?php
namespace Boxalino\DataIntegration\Api\Mview;

use Boxalino\DataIntegration\Service\ErrorHandler\MviewViewIdNotFoundException;
use Magento\Framework\Mview\View\ChangelogTableNotExistsException;

/**
 * Interface DiViewHandlerInterface
 */
interface DiViewHandlerInterface 
{

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
    public function getCurrentVersionIdByViewId(string $viewId, ?string $group = null) : int;

    /**
     * @param string $viewId
     * @param string|null $group
     * @return int
     * @throws MviewViewIdNotFoundException
     */
    public function getLastVersionIdByViewId(string $viewId, ?string $group = null) : int;

    /**
     * @param string $viewId
     * @param string|null $group
     * @return void
     * @throws MviewViewIdNotFoundException
     */
    public function clearChangelogByViewId(string $viewId, ?string $group = null) : void;

    
}
