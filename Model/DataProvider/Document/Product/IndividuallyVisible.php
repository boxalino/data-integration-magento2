<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\IndividuallyVisible as DataProviderResourceModel;

/**
 * Class IndividuallyVisible
 * Checks the visibility flag on the main store id.
 */
class IndividuallyVisible extends ModeIntegrator
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
        return $this->resourceModel->getFetchPairsByFieldsWebsiteStoreAttrIdTable(
            $this->getFields(),
            $this->getSystemConfiguration()->getWebsiteId(),
            $this->getAttributeId(),
            (int) $this->getSystemConfiguration()->getDefaultStoreId(),
            "catalog_product_entity_int"
        );
    }

    public function resolve(): void
    {
        $this->setAttributeCode("visibility");
        $this->setAttributeId((int)$this->resourceModel->getAttributeIdByAttributeCodeAndEntityTypeId(
            $this->getAttributeCode(),\Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
        );
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        $notVisibleIndividually =  \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE;

         return [
             $this->getDiIdField() => "c_p_e_s.entity_id",
             $this->getAttributeCode() => new \Zend_Db_Expr("IF(c_p_e_a_s.value = $notVisibleIndividually, NULL, 1)")
         ];
    }


}
