<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\User;

use Magento\Framework\DB\Select;

/**
 * Class Entity
 * Access the main information about the user
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\User
 */
class Entity extends ModeIntegrator
{

    /**
     * The following information is required for the user entity load
     *
     * @param array $fields
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllByFieldsWebsiteId(array $fields, string $websiteId)
    {
        $mainEntitySelect = $this->getEntityByWebsiteIdSelect($websiteId);
        $shippingSelect = $this->appendPrefixToColumnsGroupBySelect("customer_address_entity", "billing", "entity_id");
        $select = $this->adapter->select()
            ->from(
                ['c_e' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                $fields
            )
            ->joinLeft(
                ['c_a_e' => new \Zend_Db_Expr("( ". $shippingSelect->__toString() . ' )')],
                "c_a_e.billing_entity_id = c_e.default_billing",
                []
            )
            ->joinLeft(
                ['c_g' => $this->adapter->getTableName("customer_group")],
                "c_g.customer_group_id = c_e.group_id",
                ["customer_group_code"]
            )
            ->group("c_e.entity_id");

        return $this->adapter->fetchAll($select);
    }




}
