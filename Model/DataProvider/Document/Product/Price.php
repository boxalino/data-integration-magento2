<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPricePropertyInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Price as DataProviderResourceModel;

/**
 * Class Price
 */
class Price extends AttributeStrategyAbstract
    implements DocProductPropertyListInterface, DocProductPricePropertyInterface
{

    /**
     * @var array
     */
    protected $priceAttributes = [];

    /**
     *
     */
    public function resolve(): void
    {

    }


    /**
     * Loading necessary attribute details for price properties
     *
     * @return array
     */
    public function getAttributes() : array
    {
        if(empty($this->priceAttributes))
        {
            $this->priceAttributes = $this->globalResourceModel->getAttributesByScopeBackendTypeFrontendInput(
                [],
                ["decimal"],
                ["price"]
            );
        }

        return $this->priceAttributes;
    }

    function getEntityAttributeTableType(): string
    {
        return "decimal";
    }


    public function getListPrice(string $id): array
    {
        return [];
    }

    public function getSalesPrice(string $id): array
    {
        return [];
    }

    public function getCostPrice(string $id): array
    {
        return [];
    }

    public function getOtherPrices(string $id): array
    {
        return [];
    }


}
