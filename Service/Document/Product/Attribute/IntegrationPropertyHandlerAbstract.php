<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPricePropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocAttributeListInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocProductVisibilityPropertyInterface;
use Boxalino\DataIntegration\Service\Document\BasePropertyHandlerAbstract;

/**
 * Class IntegrationPropertyHandlerAbstract for doc_product context
 * Handles the data provider access for the export of doc_product properties
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
abstract class IntegrationPropertyHandlerAbstract extends BasePropertyHandlerAbstract
{

    /**
     * @var DocProductPropertyInterface | DocAttributeListInterface | DocProductPricePropertyInterface | DocProductVisibilityPropertyInterface
     */
    protected $dataProvider;

    /**
     * @return DocProductPropertyInterface | DocAttributeListInterface | DocProductPricePropertyInterface | DocProductVisibilityPropertyInterface
     */
    public function getDataProvider() : DocProductPropertyInterface
    {
        if(is_null($this->dataProvider))
        {
            $this->dataProvider = $this->diSchemaDataProviderResolver->get($this->getResolverType());
            $this->dataProvider->setHandlerIntegrateTime($this->getHandlerIntegrateTime());
            $this->dataProvider->setSyncCheck($this->getSyncCheck());
            $this->dataProvider->setMviewIds($this->getIds());
        }

        $this->_defaultDataProvider();
        return $this->dataProvider;
    }

    /**
     * Declare the updated account configurations on the data provider
     * (ex: for loop execution)
     *
     * @return void
     */
    protected function _defaultDataProvider() : void
    {
        $this->dataProvider->setAttributeCode($this->getAttributeCode())
            ->setSystemConfiguration($this->getSystemConfiguration());

        $this->dataProvider->resolve();
    }

    /**
     * The property code is set on the data provider upon looping through multiple returned attributes
     *
     * The property code is also used to identify the column in the returned content array
     * that holds the data for the property
     *
     * @return string
     */
    public function getAttributeCode() : string
    {
        if($this->handlerHasProperty($this->getResolverType()))
        {
            return $this->properties[$this->getResolverType()];
        }

        return $this->getResolverType();
    }


}
