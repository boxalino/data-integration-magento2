<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Order;

use Boxalino\DataIntegration\Api\DataProvider\GenericDocInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\ConfigurationHelperTrait;
use Boxalino\DataIntegration\Model\DataProvider\Document\DataValidationTrait;

/**
 * Abstract class ModeIntegrator
 * Holds the logic for various integration modes (instant, delta, full)
 *
 * @package Boxalino\DataIntegration\Model\DataProvider\Document\Order
 */
abstract class ModeIntegrator implements GenericDocInterface
{

    use ConfigurationHelperTrait;
    use DataValidationTrait;

    /**
     * Access property data (internal flow)
     * @return array
     */
    public function _getData(): array
    {
        return $this->getResourceModel()->getFetchAllByFieldsStoreIds($this->getFields(), $this->getSystemConfiguration()->getStoreIds());
    }

    /**
     * @return array
     */
    abstract protected function getFields() : array;

    /**
     * prepare the data provider with additional relevant elements
     */
    public function resolve(): void {}


}
