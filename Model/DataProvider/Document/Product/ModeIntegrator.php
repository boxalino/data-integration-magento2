<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyInterface;
use Boxalino\DataIntegration\Model\DataProvider\Document\AttributeValueListHelperTrait;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\ModeDisabledException;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;

/**
 * @package Boxalino\DataIntegration\Model\Document\Product
 */
abstract class ModeIntegrator implements DocProductPropertyInterface
{
    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocInstantIntegrationTrait;
    use AttributeValueListHelperTrait;

    /**
     * @var string
     */
    protected $attributeCode;

    /**
     * @var int
     */
    protected $attributeId = 0;

    public function getData() : array
    {
        /** for delta requests */
        if($this->filterByCriteria())
        {
            return $this->getDataDelta();
        }

        /** for instant updates */
        if($this->filterByIds())
        {
            if($this->hasModeEnabled())
            {
                /** @todo to be extended when instant option has been added */
                return [];
            }

            throw new ModeDisabledException("Boxalino DI: instant mode not active. Skipping sync.");
        }

        return $this->_getData();
    }

    public function getAttributeCode(): string
    {
        return $this->attributeCode;
    }

    public function getAttributeId(): int
    {
        return $this->attributeId;
    }

    public function getDataForAttribute() : array
    {
        return [];
    }

    /**
     * @param string $code
     * @return DocProductPropertyInterface
     */
    public function setAttributeCode(string $code): DocProductPropertyInterface
    {
       $this->attributeCode = $code;
       return $this;
    }

    /**
     * @param int $id
     * @return DocProductPropertyInterface
     */
    public function setAttributeId(int $id): DocProductPropertyInterface
    {
        $this->attributeId = $id;
        return $this;
    }

    /**
     * @return string
     */
    protected function _getDeltaSyncCheckDate() : string
    {
        return $this->getSyncCheck() ?? date("Y-m-d H:i", strtotime("-1 hour"));
    }

    abstract function _getData() : array;

    abstract function getDataDelta() : array;


}
