<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\MinSaleQty as DataProviderResourceModel;

/**
 * Class MinSaleQty
 * Default export for min_sale_qty information from cataloginventory_stock
 * Must be extended per project needs
 */
class MinSaleQty extends ModeIntegrator
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
		return $this->getResourceModel()->getFetchAllByFieldsWebsiteId(
			[
				$this->getDiIdField() => "c_p_e_s.entity_id",
				$this->getAttributeCode() => "c_p_e_a_s.min_sale_qty"
			],
			$this->getSystemConfiguration()->getWebsiteId()
		);
	}
	
	
}
