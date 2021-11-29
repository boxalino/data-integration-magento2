<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;

/**
 * Trait DocMviewDeltaIntegrationTrait
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
trait DocMviewDeltaIntegrationTrait
{

    /**
     * @var array
     */
    protected $ids = [];

    /**
     * @var bool 
     */
    protected $filterByIds = false;

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * @param array $ids
     * @return DocDeltaIntegrationInterface
     */
    public function setMviewIds(array $ids): DocDeltaIntegrationInterface
    {
        $this->ids = $ids;
        return $this;
    }


}
