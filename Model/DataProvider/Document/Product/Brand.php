<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeOption as DataProviderResourceModel;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Class Brand
 * Exporting brand information based on the configured attribute code
 */
class Brand extends ModeIntegrator
{

    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
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
        try{
            $data = $this->getResourceModel()->getFetchAllByWebsiteAttributeId(
                $this->getFields(),
                $this->getSystemConfiguration()->getWebsiteId(),
                $this->getAttributeId()
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
            DocSchemaInterface::FIELD_INTERNAL_ID => "c_p_e_a_s.option_id",
        ];
    }

    /**
     * Add attribute id
     * Load option id translations
     */
    public function resolve(): void
    {
        $this->setAttributeId((int)$this->getResourceModel()->getAttributeIdByAttributeCodeAndEntityTypeId(
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
            $data = $this->getResourceModel()->getFetchPairsAttributeOptionValuesByStoreAndAttributeId($this->getAttributeId(), $storeId);
            $this->attributeNameValuesList = $this->addValueToAttributeContent($data, $this->attributeNameValuesList, $languageCode);
        }
    }

}
