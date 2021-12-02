<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeOption as DataProviderResourceModel;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class Brand
 * Exporting brand information based on the configured attribute code
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
        $this->attributeNameValuesList = new \ArrayObject();
    }

    /**
     * The returned array must include content to represent:
     * product id, brand id/option id/admin value, translation of the brand in all languages
     *
     * @return array
     */
    public function _getData(): array
    {
        return $this->resourceModel->getFetchAllByWebsiteAttributeId(
            $this->getFields(),
            $this->getSystemConfiguration()->getWebsiteId(),
            $this->getAttributeId()
        );
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_a_s.value",
            DocSchemaInterface::FIELD_INTERNAL_ID => "c_p_e_a_s.option_id",
        ];
    }

    /**
     * Add attribute id
     * Load option id translations
     */
    public function resolve(): void
    {
        $this->setAttributeId((int)$this->resourceModel->getAttributeIdByAttributeCodeAndEntityTypeId(
            $this->getAttributeCode(),\Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
        );

        $this->loadOptionValues();
    }

    /**
     * pre-initialize band name translations used for content export
     */
    protected function loadOptionValues() : void
    {
        $this->attributeNameValuesList = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->resourceModel->getFetchPairsAttributeOptionValuesByStoreAndAttributeId($this->getAttributeId(), $storeId);
            $this->addValueToAttributeContent($data, $this->attributeNameValuesList, $languageCode);
        }
    }

    public function getAttributeCode() : string
    {
        return "manufacturer";
    }


}
