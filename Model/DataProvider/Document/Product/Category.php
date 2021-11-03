<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Category as DataProviderResourceModel;

/**
 * Class Category
 * Export category information
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
        return $this->resourceModel->getFetchAllByFieldsWebsite($this->getFields(), $this->getSystemConfiguration()->getWebsiteId());
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
         return [
             $this->getDiIdField() => "c_c_p.product_id",
             $this->getAttributeCode() => new \Zend_Db_Expr("GROUP_CONCAT(c_c_p.category_id SEPARATOR ',')")
         ];
    }

    function getDataDelta() : array
    {
       return [];
    }


}
