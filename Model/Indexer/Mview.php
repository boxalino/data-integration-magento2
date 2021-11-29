<?php
namespace Boxalino\DataIntegration\Model\Indexer;

use Magento\Framework\Mview\ProcessorInterface;

/**
 * Mview
 * Mview manager for the cron jobs
 *
 * @package Boxalino\DataIntegration\Model\Indexer
 */
class Mview
{

    /**
     * @var ProcessorInterface
     */
    protected $mviewProcessor;

    /**
     * BxDeltaExporter constructor.
     */
    public function __construct(ProcessorInterface $mviewProcessor)
    {
        $this->mviewProcessor = $mviewProcessor;
    }

    /**
     * Run when the MVIEW is in use (Update by Schedule)
     */
    public function clearChangeLog()
    {
        $this->mviewProcessor->clearChangelog('boxalino_di');
    }

    /**
     * Run when the MVIEW is in use (Update by Schedule)
     * Exports the tagged content in "boxalino_di_delta_*_cl"/"boxalino_di_instant_*_cl" table to Boxalino
     */
    public function update()
    {
        $this->mviewProcessor->update('boxalino_di');
    }


}
