<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductVisibilityPropertyInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Visibility as DataProviderResourceModel;

/**
 * Class Visibility
 * The logic for accessing visibility information, on a default Magento2 setup, it is same as the one for any other attribute
 *
 * For chidren that are not individually visible, the visibility flag will be set accordingly to the parent visibility
 * This is done in order to have the custom grouped/configurable child properties filterable via product_group representation
 */
class Visibility extends ModeIntegrator
    implements DocProductVisibilityPropertyInterface
{

    use ContextualAttributeTrait;

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
     * @return array
     */
    public function _getData(): array
    {
        return $this->getResourceModel()->getFetchAllEntityByFieldsWebsite(
            [$this->getDiIdField() => "c_p_e_s.entity_id"],
            $this->getSystemConfiguration()->getWebsiteId()
        );
    }

    /**
     * @param array $item
     * @return array
     */
    public function getIndividualVisibility(array $item) : array
    {
        return array_unique(array_values(array_intersect_key(
                    $this->getAsIsData($item), array_flip($this->getSystemConfiguration()->getLanguages()))
            )
        );
    }

    /**
     * Preloading visibility data for each contexts (self and context)
     */
    public function resolve(): void
    {
        $this->_resolveDataDelta();

        $this->_loadData($this->getDocPropertyNameByContext(), $this->getContextualFields());
        $this->_loadData($this->getDocPropertyNameByContext(false), $this->getAsIsFields());
    }

    /**
     * @return array
     */
    protected function getContextualFields() : array
    {
        $configurableType = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
        $groupedType = \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE;

        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => new \Zend_Db_Expr("
                (CASE
                    WHEN (c_p_e_s.type_id = '$configurableType' OR c_p_e_s.type_id = '$groupedType') THEN c_p_e_s.entity_value
                    WHEN c_p_e_s.parent_id IS NULL THEN c_p_e_s.entity_value
                    ELSE c_p_e_s.parent_value
                 END
                )"
            )
        ];
    }

    /**
     * @return array
     */
    protected function getAsIsFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_s.entity_value"
        ];
    }



}
