<?php
namespace Boxalino\DataIntegration\Model\Indexer\Product;

use Psr\Log\LoggerInterface;

/**
 * Class Delta
 * Delta Data Sync : exports to DI the products updated within a configurable time frame
 *
 * @package Boxalino\Exporter\Model\Indexer
 */
class Delta implements \Magento\Framework\Indexer\ActionInterface,
    \Magento\Framework\Mview\ActionInterface
{

    /**
     * Exporter ID in configuration
     */
    const INDEXER_ID = 'boxalino_di_delta_product';

    /**
     * DI type
     */
    const INDEXER_TYPE = 'di_delta_product';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param int $id
     */
    public function executeRow($id){}

    /**
     * @param array $ids
     */
    public function executeList(array $ids){}

    /**
     * Run when the MVIEW is in use (Update by Schedule)
     *
     * @param int[] $ids
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($ids)
    {
        $this->logger->info(get_class($this) . " -- " . __FUNCTION__);
    }

    /**
     * Run via the command line or cron job (Update on Save mode)
     *
     * The delta IDs will be accessed by checking latest updated IDs since the last SYNC CHECK time
     */
    public function executeFull()
    {
        $this->logger->info(get_class($this) . " -- " . __FUNCTION__);
    }


}
