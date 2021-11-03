<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeGlobal as GlobalDataProviderResourceModel;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\AttributeLocalized as LocalizedDataProviderResourceModel;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class AttributeStrategyAbstract
 * If the property is configurable only at the level of WEBSITE OR GLOBAL - the property is exported as GLOBAL
 * If the property is configurable at the STORE level - the property is exported as LOCALIZED
 */
abstract class AttributeStrategyAbstract extends ModeIntegrator
{

    /**
     * @var GlobalDataProviderResourceModel
     */
    protected $globalResourceModel;

    /**
     * @var LocalizedDataProviderResourceModel
     */
    protected $localizedResourceModel;

    /**
     * @var string
     */
    protected $callback;

    /**
     * @param LocalizedDataProviderResourceModel $globalResource
     * @param LocalizedDataProviderResourceModel $localizedResource
     */
    public function __construct(
        GlobalDataProviderResourceModel $globalResource,
        LocalizedDataProviderResourceModel $localizedResource
    ) {
        $this->globalResourceModel = $globalResource;
        $this->localizedResourceModel = $localizedResource;
    }

    /**
     * @return array
     */
    public function _getData(): array
    {
        return call_user_func([$this, $this->callback]);
    }

    /**
     * Identify the content-access strategy for the property
     */
    public function resolve(): void
    {
        $scope = $this->globalResourceModel->getAttributeScopeByAttrCode($this->getAttributeCode());
        if(is_null($scope))
        {
            return;
        }

        $this->setGetDataCallback("getLocalizedDataForAttribute");
        if(in_array(
            $scope,
            [ScopedAttributeInterface::SCOPE_WEBSITE, ScopedAttributeInterface::SCOPE_GLOBAL])
        ){
            $this->setGetDataCallback("getGlobalDataForAttributeAsLocalized");
        }

        $this->setAttributeId((int)$this->globalResourceModel->getAttributeIdByAttributeCodeAndEntityType(
            $this->getAttributeCode(),\Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
        );
    }

    /**
     * Used to access attribute data in case of localized
     *
     * @return array
     */
    public function getLocalizedDataForAttribute() : array
    {
        $attributeContent = new \ArrayObject();
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $data = $this->localizedResourceModel->getFetchParisForLocalizedAttributeByStoreId(
                $this->getFields(),
                $this->getSystemConfiguration()->getWebsiteId(),
                $storeId,
                $this->getAttributeId(),
                $this->getEntityAttributeTableType()
            );

            $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        return $attributeContent->getArrayCopy();
    }

    /**
     * The entity type as to access attribute information
     * @return string
     */
    abstract function getEntityAttributeTableType() : string;

    /**
     * Used to create attribute data response structure (localized) when attribute is global
     *
     * @return array
     */
    public function getGlobalDataForAttributeAsLocalized() : array
    {
        $attributeContent = new \ArrayObject();

        $data = $this->globalResourceModel->getFetchPairsForGlobalAttribute(
            $this->getFields(),
            $this->getSystemConfiguration()->getWebsiteId(),
            $this->getSystemConfiguration()->getStoreIds(),
            $this->getAttributeId(),
            $this->getEntityAttributeTableType()
        );

        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $languageCode)
        {
            $this->addValueToAttributeContent($data, $attributeContent, $languageCode, true);
        }

        return $attributeContent->getArrayCopy();
    }

    protected function setGetDataCallback(string $callback) : void
    {
        $this->callback = $callback;
    }

    function getDataDelta() : array
    {
       return [];
    }


}
