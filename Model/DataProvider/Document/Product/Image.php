<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeLocalized as DataProviderResourceModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Filesystem\DirectoryList;

/**
 * Class Image
 * Data provider for the media_image attributes
 */
class Image extends ModeIntegrator
    implements DocProductPropertyListInterface
{

    /**
     * @var array
     */
    protected $imagePlaceholdersList;

    /**
     * @var DataProviderResourceModel
     */
    private $resourceModel;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string | null
     */
    protected $placeholder;

    /**
     * @var DirectoryList
     */
    protected $directory;

    /**
     * @param DataProviderResourceModel $resource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        DataProviderResourceModel $resource,
        ScopeConfigInterface $scopeConfig,
        DirectoryList $directoryList
    ) {
        $this->resourceModel = $resource;
        $this->scopeConfig = $scopeConfig;
        $this->directory = $directoryList;
    }

    /**
     * @return array
     */
    public function _getData(): array
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $this->placeholder = $this->imagePlaceholdersList[$this->getAttributeCode()][$storeId];
            $data = $this->resourceModel->getFetchPairsForLocalizedAttributeByStoreId(
                $this->getFields(),
                $this->getSystemConfiguration()->getWebsiteId(),
                $storeId,
                $this->getAttributeId(),
                "varchar"
            );

            $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        return $attributeContent->getArrayCopy();
    }

    /**
     * Accessing the details about the media_image attributes linked to product
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->resourceModel->getAttributesByScopeBackendTypeFrontendInput(
            [],
            ["varchar"],
            ["media_image"]
        );
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        $mediaPath = $this->getMediaAbsoluteUrl();
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() =>
                new \Zend_Db_Expr("CONCAT('$mediaPath', IF(c_p_e_a_s.value IS NULL OR c_p_e_a_s.value = '', '$this->placeholder', c_p_e_a_s.value))")
        ];
    }

    public function resolve(): void
    {
        $this->loadImagePlaceholders();
    }

    /**
     * Adding the default value for when a product image is missing
     * - if placeholder is missing - label is set
     */
    protected function loadImagePlaceholders() : void
    {
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId=>$languageCode)
        {
            foreach($this->getAttributes() as $attribute)
            {
                $attributeCode = $attribute["attribute_code"];
                $this->imagePlaceholdersList[$attributeCode][$storeId] = $this->scopeConfig->getValue(
                        "catalog/placeholder/{$attributeCode}_placeholder",
                        ScopeInterface::SCOPE_STORE,
                        $storeId) ?? "{$attributeCode}_placeholder";
            }
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMediaAbsoluteUrl() : string
    {
        return $this->directory->getUrlPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA) . "/catalog/product";
    }

    function getDataDelta() : array
    {
        return [];
    }


}
