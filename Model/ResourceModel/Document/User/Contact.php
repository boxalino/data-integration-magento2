<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\User;

use Magento\Framework\DB\Select;

/**
 * Class Contact
 * Access the data about user addresses (billing and shipping)
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\User
 */
class Contact extends ModeIntegrator
{

    /**
     * @param array $fields
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllByFieldsWebsiteId(array $fields, string $websiteId) : array
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $selectBilling = $this->adapter->select()
            ->from(
                ['c_e' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['c_e_a' => $this->adapter->getTableName("customer_address_entity")],
                "c_e_a.entity_id = c_e.default_billing",
                [new \Zend_Db_Expr("'billing' as address_type")]
            );

        $selectShipping = $this->adapter->select()
            ->from(
                ['c_e' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['c_e_a' => $this->adapter->getTableName("customer_address_entity")],
                "c_e_a.entity_id = c_e.default_shipping",
                [new \Zend_Db_Expr("'shipping' as address_type")]
            );

        $select = $this->adapter->select()
            ->union(
                [$selectBilling, $selectShipping],
                \Magento\Framework\DB\Select::SQL_UNION_ALL
            );

        return $this->adapter->fetchAll($select);
    }



}
