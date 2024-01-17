<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Entity;

use Magento\Framework\DB\Select;

trait ProductResourceTrait
{
    /**
     * Generic ENTITY SELECT for FULL export
     *
     * @param string $websiteId
     * @return Select
     */
    protected function _getProductEntityByWebsiteIdSelect(string $websiteId): Select
    {
        return $this->adapter->select()
            ->from(
                ['e' => $this->adapter->getTableName('catalog_product_entity')],
                ["*"]
            )
            ->joinLeft(
                ['c_p_w' => $this->adapter->getTableName('catalog_product_website')],
                'e.entity_id = c_p_w.product_id AND c_p_w.website_id = ' . $websiteId,
                []
            )
            ->where("c_p_w.website_id= ? " , $websiteId);
    }

    /**
     * Filter out parent_ids which no longer exist in the DB
     *
     * @return Select
     */
    protected function getProductRelationEntityTypeSelect() : Select
    {
        return $this->adapter->select()
            ->from(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                ["parent_id", "child_id"]
            )
            ->joinLeft(
                ['c_p_e' => $this->adapter->getTableName('catalog_product_entity')],
                "c_p_r.parent_id = c_p_e.entity_id",
                ["parent_type_id" => "type_id"]
            )
            ->where("c_p_e.entity_id IS NOT NULL");
    }
}
