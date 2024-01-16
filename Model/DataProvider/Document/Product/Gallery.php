<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductPropertyListInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Gallery as DataProviderResourceModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Filesystem\DirectoryList;

/**
 * Class Gallery
 * Data provider for the media_gallery attributes
 */
class Gallery extends ModeIntegrator
    implements DocProductPropertyListInterface
{

    /**
     * @var bool
     */
    protected $addMediaPath;

    /**
     * @var DirectoryList
     */
    protected $directory;

    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
     * @param ScopeConfigInterface $scopeConfig
     * @param DirectoryList $directoryList
     */
    public function __construct(
        DataProviderResourceModel $resource,
        DirectoryList $directoryList,
        bool $addMediaPath = true
    ) {
        $this->resourceModel = $resource;
        $this->directory = $directoryList;
        $this->addMediaPath = $addMediaPath;
    }

    /**
     * @return array
     */
    public function _getData(): array
    {
        return $this->getResourceModel()->getFetchPairsByFieldsWebsite(
            $this->getFields(),
            $this->getSystemConfiguration()->getWebsiteId(),
            $this->getAttributeId()
        );
    }

    /**
     * Accessing the details about the gallery attributes linked to product
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->getResourceModel()->getAttributesByScopeBackendTypeFrontendInput(
            [],
            [],
            ["gallery"]
        );
    }

    /**
     * @return array
     */
    protected function getFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => new \Zend_Db_Expr($this->_getValueField())
        ];
    }


    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMediaAbsoluteUrl() : string
    {
        return $this->directory->getUrlPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA) . "/catalog/product";
    }

    /**
     * @return string
     */
    protected function _getValueField() : string
    {
        if($this->addMediaPath)
        {
            $mediaPath = $this->getMediaAbsoluteUrl();
            return "GROUP_CONCAT(CONCAT('$mediaPath', IF(c_p_e_a_s.value IS NULL OR c_p_e_a_s.value = '', NULL, c_p_e_a_s.value)) SEPARATOR ',')";
        }

        return "GROUP_CONCAT(IF(c_p_e_a_s.value IS NULL OR c_p_e_a_s.value = '', NULL, c_p_e_a_s.value) SEPARATOR ',')";
    }


}
