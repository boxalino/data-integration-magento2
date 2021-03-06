<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document;

/**
 * Interface DiSchemaDataProviderResourceInterface
 */
trait DiSchemaDataProviderResourceTrait
{

    protected $useDeltaIdsConditionals = false;

    /**
     * @var bool
     */
    protected $delta = false;

    /**
     * @var bool
     */
    protected $instant = false;

    /**
     * @var string
     */
    protected $dateConditional;

    /**
     * @var array
     */
    protected $idsConditional = [];

    /**
     * @var int
     */
    protected $batchSize = 0;

    /**
     * @var string
     */
    protected $chunk = "0";

    /**
     * @param string $dateCriteria
     * @param array|string[] $conditionalFields
     */
    public function addDateConditional(string $dateCriteria) : void
    {
        $this->dateConditional = $dateCriteria;
    }

    /**
     * @param array $ids
     * @param string $field
     */
    public function addIdsConditional(array $ids) : void
    {
        $this->idsConditional = $ids;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function useDelta(bool $value) : void
    {
        $this->delta = $value;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function useInstant(bool $value) : void
    {
        $this->instant = $value;
    }

    /**
     * @param bool $value
     */
    public function useDeltaIdsConditionals(bool $value) : void
    {
        $this->useDeltaIdsConditionals = $value;
    }

    /**
     * @param int $batchSize
     * @return void
     */
    public function setBatchSize(int $batchSize) : void
    {
        $this->batchSize = $batchSize;
    }

    /**
     * @return int
     */
    public function getBatch() : int
    {
        return $this->batchSize;
    }

    /**
     * @return string
     */
    public function getChunk(): string
    {
        return (string)$this->chunk;
    }

    /**
     * @param string $chunk
     */
    public function setChunk(string $chunk): void
    {
        $this->chunk = $chunk;
    }


}
