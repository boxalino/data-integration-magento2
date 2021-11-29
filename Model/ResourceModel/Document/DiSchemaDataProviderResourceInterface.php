<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document;

use Magento\Framework\DB\Select;

/**
 * Interface DiSchemaDataProviderResourceInterface
 */
interface DiSchemaDataProviderResourceInterface
{

    /**
     * @param string $dateCriteria
     */
    public function addDateConditional(string $dateCriteria) : void;

    /**
     * @param array $ids
     */
    public function addIdsConditional(array $ids) : void;

    /**
     * @param bool $value
     * @return void
     */
    public function useDelta(bool $value) : void;

    /**
     * @param bool $value
     * @return void
     */
    public function useInstant(bool $value) : void;

    /**
     * @param bool $value
     */
    public function useDateIdsConditionals(bool $value) : void;


}
