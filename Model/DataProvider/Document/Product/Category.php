<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Category as DataProviderResourceModel;

/**
 * Class Category
 */
class Category extends ModeIntegrator
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
     * @return array
     */
    public function _getData(): array
    {
        return $this->resourceModel->getDataByFieldsWebsite($this->getFields(), $this->getSystemConfiguration()->getWebsiteId());
    }

    public function resolve(): void {}

    /**
     * @return array
     */
    protected function getFields() : array
    {
         return [
             new \Zend_Db_Expr("c_c_p.product_id AS {$this->getDiIdField()}"),
             new \Zend_Db_Expr("GROUP_CONCAT(c_c_p.category_id SEPARATOR ',') AS " . $this->getAttributeCode())
         ];
    }

    function getDataDelta() : array
    {
       return [];
    }


}
