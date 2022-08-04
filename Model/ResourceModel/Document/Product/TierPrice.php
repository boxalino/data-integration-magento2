<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Magento\Framework\DB\Select;

/**
 * Class Tier Price
 * Accessing tier price values
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class TierPrice extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllByFieldsWebsiteId(array $fields, string $websiteId) : array
    {
        $select = $this->adapter->select()
            ->from(
                ["e" => new \Zend_Db_Expr("( " . $this->getGroupSelectByWebsiteId($websiteId)->__toString() . ")")],
                $fields
            );

        return $this->adapter->fetchAll($select);
    }

    /**
     * @param string $websiteId
     * @return Select
     */
    protected function getGroupSelectByWebsiteId(string $websiteId) : Select
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $tierPriceSelect = $this->_getTierPriceJoinSelect(
            $this->getTierPriceByWebsiteIdSelect(0),
            $this->getTierPriceByWebsiteIdSelect((int)$websiteId),
            [
                "c_p_e_t_p.entity_id", "c_p_e_t_p.customer_group_id", "c_p_e_t_p.qty",
                new \Zend_Db_Expr("IF(c_p_e_t_p_w.value IS NULL, c_p_e_t_p.value, c_p_e_t_p_w.value) AS value")
            ]
        );

        $valuesGroupSelect = $this->adapter->select()
            ->from(
                ["main" => new \Zend_Db_Expr("( ". $tierPriceSelect->__toString() . ' )')],
                [
                    "main.entity_id", "main.customer_group_id",
                    new \Zend_Db_Expr("JSON_ARRAYAGG(JSON_OBJECT('qty', CAST(qty AS DECIMAL),'value', value)) AS tier_price")
                ]
            )
            ->group(["main.entity_id","main.customer_group_id"]);

        $select = $this->adapter->select()
            ->from(
                ['t_p' => new \Zend_Db_Expr("( ". $valuesGroupSelect->__toString() . ' )')],
                [
                    't_p.entity_id',
                    new \Zend_Db_Expr("JSON_ARRAYAGG(JSON_OBJECT('customer_group_id',customer_group_id,'values', tier_price)) AS value")
                ]
            )
            ->joinLeft(
                ["c_p_e_s" => new \Zend_Db_Expr("( " . $mainEntitySelect->__toString() . " )")],
                "c_p_e_s.entity_id = t_p.entity_id",
                []
            )
            ->where("c_p_e_s.entity_id IS NOT NULL")
            ->group("t_p.entity_id");

        return $select;
    }

    /**
     * @param string $websiteId
     * @return array
     */
    public function getFetchPairsByAllGroupsMinQty(string $websiteId) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $tierPriceSelect = $this->_getTierPriceJoinSelect(
            $this->getTierPriceByWebsiteIdAllGroupsMinQtySelect(0),
            $this->getTierPriceByWebsiteIdAllGroupsMinQtySelect((int)$websiteId),
            ["c_p_e_t_p.entity_id", new \Zend_Db_Expr("IF(c_p_e_t_p_w.value IS NULL, c_p_e_t_p.value, c_p_e_t_p_w.value) AS value")]
        );

        $select = $this->adapter->select()
            ->from(
                ['t_p' => new \Zend_Db_Expr("( ". $tierPriceSelect->__toString() . ' )')],
                ['t_p.entity_id',"t_p.value"]
            )
            ->joinLeft(
                ["c_p_e_s" => new \Zend_Db_Expr("( " . $mainEntitySelect->__toString() . " )")],
                "c_p_e_s.entity_id = t_p.entity_id",
                []
            )
            ->where("c_p_e_s.entity_id IS NOT NULL");

        return $this->adapter->fetchPairs($select);
    }

    /**
     * Do a join for tier price access between website_id=0 and a specific website_id
     * @param Select $tierPriceGlobal
     * @param Select $tierPriceWebsite
     * @param array $fields
     * @return Select
     */
    protected function _getTierPriceJoinSelect(Select $tierPriceGlobal, Select $tierPriceWebsite, array $fields) : Select
    {
        return $this->adapter->select()
            ->from(
                ['c_p_e_t_p' => new \Zend_Db_Expr("( ". $tierPriceGlobal->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['c_p_e_t_p_w' => new \Zend_Db_Expr("( ". $tierPriceWebsite->__toString() . ' )')],
                "c_p_e_t_p.entity_id = c_p_e_t_p_w.entity_id AND c_p_e_t_p.customer_group_id=c_p_e_t_p_w.customer_group_id AND c_p_e_t_p.qty = c_p_e_t_p_w.qty AND c_p_e_t_p_w.all_groups=c_p_e_t_p.all_groups",
                []
            );
    }

    /**
     * @param string $websiteId
     * @return Select
     */
    public function getTierPriceByWebsiteIdAllGroupsMinQtySelect(int $websiteId = 0): Select
    {
        return $this->adapter->select()
            ->from(
                array('c_p_t_p' => $this->adapter->getTableName('catalog_product_entity_tier_price')),
                [
                    'entity_id', 'website_id', 'all_groups', 'customer_group_id',
                    new \Zend_Db_Expr("MIN(qty) AS qty"),
                    new \Zend_Db_Expr("IF(percentage_value IS NULL, ROUND(value,2), CONCAT(percentage_value, '%')) AS value")
                ]
            )
            ->where('website_id = ?', $websiteId)
            ->where('all_groups = 1')
            ->group("entity_id");
    }

    /**
     * @param int $websiteId
     * @return Select
     */
    public function getTierPriceByWebsiteIdSelect(int $websiteId): Select
    {
        return $this->adapter->select()
            ->from(
                array('c_p_t_p' => $this->adapter->getTableName('catalog_product_entity_tier_price')),
                [
                    'entity_id', 'qty', 'all_groups',
                    new \Zend_Db_Expr("IF(all_groups = 1, 'ALL', customer_group_id) AS customer_group_id"),
                    new \Zend_Db_Expr("IF(percentage_value IS NULL, ROUND(value,2), CONCAT(percentage_value, '%')) AS value")
                ]
            )
            ->where('website_id = ?', $websiteId);
    }


}
