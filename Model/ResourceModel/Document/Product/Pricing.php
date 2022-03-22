<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Magento\Framework\DB\Select;

/**
 * Class Pricing
 * Accessing indexed_price logic for pricing
 * (mainly used for creating labels)
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Pricing extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllByFieldsWebsite(array $fields, string $websiteId) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $indexedPriceSelect = $this->getIndexedPriceByWebsiteIdSelect($websiteId);
        $select = $this->adapter->select()
            ->from(
                ['c_p_e_s' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            )
            ->join(
                ['c_p_e_a_s' => new \Zend_Db_Expr("( ". $indexedPriceSelect->__toString() . ' )')],
                "c_p_e_s.entity_id = c_p_e_a_s.entity_id",
                []
            );

        return $this->adapter->fetchAll($select);
    }

    /**
     * @param string $websiteId
     * @param int $customerGroupId
     * @return Select
     */
    public function getIndexedPriceByWebsiteIdSelect(string $websiteId, int $customerGroupId = 0): Select
    {
        return $this->adapter->select()
            ->from(
                array('c_p_i' => $this->adapter->getTableName('catalog_product_index_price')),
                ['*']
            )
            ->where('website_id=?', $websiteId)
            ->where('customer_group_id = ?', $customerGroupId);
    }


}
