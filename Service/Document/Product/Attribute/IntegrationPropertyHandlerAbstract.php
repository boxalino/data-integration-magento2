<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Product\Attribute;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPricePropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocAttributeListInterface;
use Boxalino\DataIntegration\Service\Document\BaseIntegrationPropertyHandlerAbstract;
use Boxalino\DataIntegrationDoc\Doc\DocProductAttributeTrait;

/**
 * Class IntegrationPropertyHandlerAbstract for doc_product context
 * Handles the data provider access for the export of doc_product properties
 *
 * @package Boxalino\DataIntegration\Service\Document\Product\Attribute
 */
abstract class IntegrationPropertyHandlerAbstract extends BaseIntegrationPropertyHandlerAbstract
{

    use DocProductAttributeTrait;

    /**
     * @var DocProductPropertyInterface | DocAttributeListInterface
     */
    protected $dataProvider;

    /**
     * @return DocProductPropertyInterface | DocAttributeListInterface | DocProductPricePropertyInterface
     */
    public function getDataProvider() : DocProductPropertyInterface
    {
        if(is_null($this->dataProvider))
        {
            $this->dataProvider = $this->diSchemaDataProviderResolver->get($this->getResolverType());
            $this->dataProvider->setAttributeCode($this->getAttributeCode())
                ->setSystemConfiguration($this->getSystemConfiguration());
            $this->dataProvider->setHandlerIntegrateTime($this->getHandlerIntegrateTime());

            $this->dataProvider->resolve();
        }

        return $this->dataProvider;
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
        return $this->getResolverType();
    }


}
