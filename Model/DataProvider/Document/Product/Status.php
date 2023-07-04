<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Api\DataProvider\DocProductContextualPropertyInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Status as DataProviderResourceModel;

/**
 * Class Status
 * If the status is configurable only at the level of WEBSITE OR GLOBAL - the property is exported as GLOBAL
 * If the status is configurable at the STORE level - the property is exported as LOCALIZED
 *
 * For grouped / configurable products - the product_group status depends on the availability of children to be bought
 * (ex: status, stock, etc)
 *
 * This logic is configurable
 */
class Status extends ModeIntegrator
    implements DocProductContextualPropertyInterface
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
     * Preloading visibility data for each contexts (self and context)
     */
    public function resolve(): void
    {
        $this->_resolveDataDelta();

        $this->_loadData($this->getDocPropertyNameByContext(false), $this->getAsIsFields());
        $this->_loadData($this->getDocPropertyNameByContext(), $this->getContextualFields());
    }

    /**
     * @return array
     */
    protected function getAsIsFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_s.value"
        ];
    }

    /**
     * @return array
     */
    protected function getContextualFields() : array
    {
        return [
            $this->getDiIdField() => "c_p_e_s.entity_id",
            $this->getAttributeCode() => "c_p_e_s.contextual"
        ];
    }



}
