<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Brand as DataProviderResourceModel;

/**
 * Class Brand
 */
class Brand extends ModeIntegrator
{

    /**
     * @var DataProviderResourceModel
     */
    private $resourceModel;

    /**
     * @param DataProviderResourceModel $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;
    }

    /**
     * The returned array must include content to represent:
     * product id, brand id/option id/admin value, translation of the brand in all languages
     *
     * @return array
     */
    public function _getData(): array
    {
        return [];
    }

    public function resolve(): void {}

    /**
     * @return array
     */
    protected function getFields() : array
    {
         return [];
    }

    public function getAttributeCode() : string
    {
        return "manufacturer";
    }

    function getDataDelta() : array
    {
       return [];
    }


}
