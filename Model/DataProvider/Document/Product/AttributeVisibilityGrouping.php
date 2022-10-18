<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeGlobal as DataProviderResourceModel;

/**
 * Class AttributeVisibilityGrouping
 *
 * The attribute used to identify the grouping visibility attribute can either be: varchar(1) or option (2).
 * It is expected to use the global value of it (it is not store-view based, but website)
 *
 * Will be extended to support backend type #2 (option)
 */
class AttributeVisibilityGrouping extends ModeIntegrator
{

    /** @var string  */
    protected $eavAttributeType = "varchar";

    /**
     * @param DataProviderResourceModel $attributeGlobal
     */
    public function __construct(
        DataProviderResourceModel $attributeGlobal
    ) {
        $this->resourceModel = $attributeGlobal;
    }

    /**
     * The returned array must include content to represent:
     * product id, attribute value (array, imploded by ,)
     *
     * @return array
     */
    public function _getData(): array
    {
        try{
            $data = $this->getResourceModel()->getSelectAllForGlobalAttribute(
                $this->getFields(),
                $this->getSystemConfiguration()->getWebsiteId(),
                $this->getSystemConfiguration()->getStoreIds(),
                $this->getAttributeId(),
                $this->eavAttributeType
            );
        } catch (\Throwable $exception)
        {
            $data = [];
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_a_s.value",
        ];
    }

    /**
     * Add attribute id
     */
    public function resolve(): void
    {
        $this->setAttributeId((int)$this->getResourceModel()->getAttributeIdByAttributeCodeAndEntityTypeId(
            $this->getAttributeCode(), \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
        );
    }


}
