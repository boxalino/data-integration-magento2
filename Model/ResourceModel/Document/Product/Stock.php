<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Magento\Framework\DB\Select;

/**
 * Class Stock
 * Logic for accessing the stock of the SKU (product)
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Stock extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllByFieldsWebsite(array $fields, string $websiteId) : array
    {
        $stockEntitySelect = $this->getStockInformation();
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
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
    public function getStockInformation() : Select
    {
        return $this->adapter->select()
            ->from(
                ["c_s_s" => $this->adapter->getTableName('cataloginventory_stock_status')],
                ['product_id', 'stock_status', 'qty']
            )
            ->joinLeft(
                ["c_s" => $this->adapter->getTableName('cataloginventory_stock')],
                "c_s.stock_id = c_s_s.stock_id AND c_s.website_id = c_s_s.website_id",
                ["stock_name"]
            )
            ->group("c_s_s.product_id");
    }


}
