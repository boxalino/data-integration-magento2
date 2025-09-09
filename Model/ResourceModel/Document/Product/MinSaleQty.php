<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Magento\Framework\DB\Select;

/**
 * Class MinSaleQty
 * Logic for accessing the MinSaleQty of the SKU (product)
 */
class MinSaleQty extends ModeIntegrator
{
	
	/**
	 * @param array $fields
	 * @param string $websiteId
	 * @return array
	 */
	public function getFetchAllByFieldsWebsiteId(array $fields, string $websiteId) : array
	{
		$stockEntitySelect = $this->getStockInformation();
		$select = $this->adapter->select()
			->from(
				['c_p_e_s' => new \Zend_Db_Expr("( ". $this->getEntityByWebsiteIdSelect($websiteId)->__toString() . ' )')],
				$fields
			)
			->join(
				['c_p_e_a_s' => new \Zend_Db_Expr("( ". $stockEntitySelect->__toString() . ' )')],
				"c_p_e_a_s.product_id = c_p_e_s.entity_id"
			);
		
		return $this->adapter->fetchAll($select);
	}
	
	/**
	 * @return Select
	 */
	protected function getStockInformation() : Select
	{
		$select = $this->adapter->select()
			->from(
				["c_s_i" => $this->adapter->getTableName('cataloginventory_stock_item')],
				['product_id', 'min_sale_qty']
			)
			->group("c_s_i.product_id");
		
		return $select;
	}
	
	
}
