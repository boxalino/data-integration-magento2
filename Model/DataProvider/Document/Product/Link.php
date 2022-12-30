<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Link as DataProviderResourceModel;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Link
 *
 * The product link can be accessed via a series of logical flows:
 * 1. export from url_rewrite (if "Use Url Server Rewrites" is enabled)
 * 2. export with/without product suffix (as configured)
 * 3. export with/without category path (as configured)
 *
 * In this sample, the 2nd strategy is used.
 * To be extended/replaced per project needs
 */
class Link extends ModeIntegrator
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Cache for product rewrite suffix
     *
     * @var array
     */
    protected $productUrlSuffix = [];

    /**
     * @var string | null
     */
    protected $suffix;

    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        DataProviderResourceModel $resource,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->resourceModel = $resource;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function _getData(): array
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $this->suffix = $this->getProductUrlSuffix($storeId);

            $data = $this->getResourceModel()->getFetchPairsByFieldsWebsiteStore(
                $this->getFields(),
                $this->getSystemConfiguration()->getWebsiteId(),
                $storeId
            );

            $attributeContent = $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        return $attributeContent->getArrayCopy();
    }

    /**
     * Adding helper elements to the data provider
     */
    public function resolve(): void
    {
        $this->loadProductUrlSuffix();
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => new \Zend_Db_Expr("CONCAT(c_p_e_s.value, '$this->suffix')")
        ];
    }

    /**
     * Add available product link configurations
     */
    protected function loadProductUrlSuffix() : void
    {
        foreach($this->getSystemConfiguration()->getStoreIds() as $storeId)
        {
            $this->productUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
    }

    /**
     * Retrieve product rewrite suffix for store
     *
     * @param int $storeId
     * @return string|null
     */
    protected function getProductUrlSuffix(int $storeId) : ?string
    {
        return $this->productUrlSuffix[$storeId];
    }



}
