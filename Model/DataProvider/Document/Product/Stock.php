<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\Stock as DataProviderResourceModel;

/**
 * Class Stock
 * Default export for stock information from cataloginventory_stock
 * Must be extended per project needs
 */
class Stock extends ModeIntegrator
{

    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;
    }

    /**
     * @return array
     */
    public function _getData(): array
    {
        return $this->getResourceModel()->getFetchAllByFieldsWebsite(
            $this->getFields(), $this->getSystemConfiguration()->getWebsiteId()
        );
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
       return [
           $this->getDiIdField() => "c_p_e_s.entity_id",
           $this->getAttributeCode() => "c_p_e_a_s.qty",
           "c_p_e_a_s.stock_name",
           "c_p_e_a_s.stock_status"
       ];
    }


}
