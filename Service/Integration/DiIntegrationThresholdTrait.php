<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Integration;

use Boxalino\DataIntegrationDoc\Service\Flow\ThresholdCheckTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\FullIntegrationInterface;

/**
 * Trait DiIntegrationConfigurationTrait
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
trait DiIntegrationThresholdTrait
{

    use ThresholdCheckTrait;

    /**
     * @var float
     */
    protected $fullConversionThreshold = 0;

    /**
     * @var int | null
     */
    protected $threshold;

    /**
     * @return void
     */
    public function reviewModeBasedOnSyncSize(int $size) : void
    {
        if($this->getFullConversionThreshold() > 0)
        {
            $this->getThreshold();
            if($this->threshold > 0)
            {
                if($size > $this->threshold*$this->getFullConversionThreshold())
                {
                    try {
                        $this->getLogger()->info(
                            "Boxalino DI: upgrading sync mode to F: " . $size . " is bigger than allowed "
                            . $this->getFullConversionThreshold() . " threshold from " . $this->threshold
                        );
                    } catch (\Throwable $exception) {}

                    try{
                        $this->getSystemConfiguration()->setMode(FullIntegrationInterface::INTEGRATION_MODE);
                        $this->setMviewIds([]);
                    } catch (\Throwable $exception) {}
                }
            }
        }
    }

    /**
     * @return int|null
     */
    protected function getThreshold() : ?int
    {
        try{
            $this->threshold = $this->thresholdCheck();
        } catch (\Throwable $exception)
        {
            $this->threshold = 0;
        }

        return $this->threshold;
    }

    /**
     * @param int|null $threshold
     * @return void
     */
    protected function setThreshold(?float $threshold) : void
    {
        $this->threshold = $threshold;
    }

    /**
     * @param float $fullConversionThreshold
     * @return void
     */
    protected function setFullConversionThreshold(float $fullConversionThreshold) : void
    {
        $this->fullConversionThreshold = $fullConversionThreshold;

        if($fullConversionThreshold > 1)
        {
            $fullConversionThreshold = $fullConversionThreshold/100;
        }

        if($fullConversionThreshold > 0)
        {
            $this->fullConversionThreshold = round($fullConversionThreshold, 4);
        }
    }

    /**
     * % limit after which the export mode is converted to 'full' export
     *
     * @return float
     */
    protected function getFullConversionThreshold() : float
    {
        return $this->fullConversionThreshold;
    }


}
