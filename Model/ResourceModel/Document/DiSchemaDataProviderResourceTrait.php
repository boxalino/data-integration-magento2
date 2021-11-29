<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document;

/**
 * Interface DiSchemaDataProviderResourceInterface
 */
trait DiSchemaDataProviderResourceTrait
{

    protected $useDateIdsConditionals = false;

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
    public function useDateIdsConditionals(bool $value) : void
    {
        $this->useDateIdsConditionals = $value;
    }


}
