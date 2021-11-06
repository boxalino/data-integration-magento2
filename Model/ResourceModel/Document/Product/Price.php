<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

/**
 * Class Price
 *
 * By default, just the default currency values are exported
 * For more options - please customize
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class Price extends ModeIntegrator
{

    /**
     * @return array
     */
    public function getFetchAllByFieldsWebsite(string $type, string $key): array
    {
        $select = $this->getPriceSqlByType($type, $key);
        return $this->adapter->fetchAll($select);
    }

    /**
     * @param string $type
     * @param string $key
     * @return \Magento\Framework\DB\Select
     */
    public function getPriceSqlByType(string $type, string $key): \Magento\Framework\DB\Select
    {
        $statusId = $this->getAttributeIdByAttributeCodeAndEntityTypeId('status', \Magento\Catalog\Setup\CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        $select = $this->adapter->select()
            ->from(
                array('c_p_r' => $this->adapter->getTableName('catalog_product_relation')),
                array('parent_id')
            )
            ->join(
                array('t_d' => $this->adapter->getTableName('catalog_product_entity_' . $type)),
                't_d.entity_id = c_p_r.child_id',
                array(
                    'value' => 'MIN(t_d.value)'
                )
            )->join(
                array('t_s' => $this->adapter->getTableName('catalog_product_entity_int')),
                't_s.entity_id = c_p_r.child_id AND t_s.value = 1',
                array()
            )
            ->where('t_d.attribute_id = ?', $key)
            ->where('t_s.attribute_id = ?', $statusId)
            ->group(array('parent_id'));

        return $select;
    }

    /**
     * @param string $websiteId
     * @return array
     */
    public function getDistinctCustomerGroupIdsForPriceByWebsiteId(string $websiteId) : array
    {
        $select = $this->adapter->select()
            ->from(
                $this->adapter->getTableName('catalog_product_index_price'),
                [new \Zend_Db_Expr("DISTINCT(customer_group_id) AS customer_group_id")]
            )
            ->where('website_id = ?', $websiteId);

        return $this->adapter->fetchCol($select);
    }

    /**
     * @param string $type
     * @param string $websiteId
     * @param string $customerGroupId
     * @return array
     */
    public function getIndexedPriceForCustomerGroup(string $type, string $websiteId, string $customerGroupId) : array
    {
        $select = $this->adapter->select()
            ->from(
                array('c_p_i' => $this->adapter->getTableName('catalog_product_index_price')),
                ['entity_id', 'value'=> $type . "_price"]
            )
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $customerGroupId)
            ->group(['entity_id']);

        return $this->adapter->fetchAll($select);
    }


}
