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

    use AttributeLocalizedTrait;
    use AttributeGlobalTrait;

    /**
     * @var bool
     */
    protected $isLocalized = true;

    /**
     * @param GlobalDataProviderResourceModel $globalResource
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
     * The entity type as to access attribute information
     * @return string
     */
    abstract function getEntityAttributeTableType() : string;

    /**
     * @return array
     */
    public function _getData(): array
    {
        if($this->isLocalized)
        {
            return $this->getLocalizedDataForAttribute();
        }

        return $this->getGlobalDataForAttributeAsLocalized();
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
        $this->_setGetDataStrategy($scope);

        $this->setAttributeId((int)$this->globalResourceModel->getAttributeIdByAttributeCodeAndEntityTypeId(
            $this->getAttributeCode(),\Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID)
        );
    }

    /**
     * @param string $scope
     */
    protected function _setGetDataStrategy(string $scope) : void
    {
        if(in_array(
            $scope,
            [ScopedAttributeInterface::SCOPE_WEBSITE, ScopedAttributeInterface::SCOPE_GLOBAL])
        ){
            $this->setIsLocalized(false);
        }
    }

    /**
     * @param bool $isLocalized
     */
    protected function setIsLocalized(bool $isLocalized) : void
    {
        $this->isLocalized = $isLocalized;
    }

    function getDataDelta() : array
    {
       return [];
    }


}
