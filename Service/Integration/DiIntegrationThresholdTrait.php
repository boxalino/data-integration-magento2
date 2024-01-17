<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration;

use Boxalino\DataIntegrationDoc\Service\Integration\Mode\FullIntegrationInterface;

/**
 * Trait DiIntegrationThresholdTrait
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
trait DiIntegrationThresholdTrait
{

    /**
     * The number of IDs allowed in a single delta push
     * (implemented in order to avoid SQL performance issues)
     *
     * @var int
     */
    protected $fullConversionThreshold = 0;

    /**
     * @return void
     */
    public function reviewModeBasedOnSyncSize(int $size) : void
    {
        $this->getLogger()->info(
            "Boxalino DI: $size items found for {$this->getLogProcessName()}"
        );

        if($this->getFullConversionThreshold() > 0)
        {
            if($size > $this->getFullConversionThreshold())
            {
                try {
                    $this->getLogger()->info(
                        "Boxalino DI: upgrading {$this->getLogProcessName()} sync mode to F: " . $size
                        . " products is bigger than allowed margin of " . $this->getFullConversionThreshold()
                    );
                } catch (\Throwable $exception) {}

                try{
                    $this->getSystemConfiguration()->setMode(FullIntegrationInterface::INTEGRATION_MODE);
                    $this->setMviewIds([]);
                } catch (\Throwable $exception) {}
            }
        }
    }

    /**
     * @param int $fullConversionThreshold
     * @return void
     */
    protected function setFullConversionThreshold(int $fullConversionThreshold) : void
    {
        $this->fullConversionThreshold = $fullConversionThreshold;
    }

    /**
     * number of ids limit after which the export mode is converted to 'full' export
     *
     * @return float
     */
    protected function getFullConversionThreshold() : float
    {
        return $this->fullConversionThreshold;
    }


}
