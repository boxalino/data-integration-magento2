<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

/**
 * Class Stock
 * Logic for accessing the stock of the SKU (product)
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Stock extends ModeIntegrator
{

    /**
     * @return array
     */
    public function getStockInformation() : array
    {
        $select = $this->adapter->select()
            ->from(
                $this->adapter->getTableName('cataloginventory_stock_status'),
                ['entity_id' => 'product_id', 'stock_status', 'qty']
            )
            ->where('stock_id = ?', 1);

//            $select->where('product_id IN(?)', $this->exportIds);

        return $this->adapter->fetchAll($select);
    }

}
