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
                'e.entity_id = c_p_w.product_id',
                []
            )
            ->where("c_p_w.website_id= ? " , $websiteId);
    }

    /**
     * Filter out parent_ids that are not belonging to the website
     * Filter out parent_id/child_ids that do not belong to the website
     *
     * @param string $field
     * @param string | null $website
     * @return Select
     */
    protected function getProductRelationByFieldWebsiteSelect(string $field, ?string $websiteId = null) : Select
    {
        $select = $this->adapter->select()
            ->from(
                ['c_p_r' => $this->adapter->getTableName('catalog_product_relation')],
                ["parent_id", "child_id"]
            );

        if(is_null($websiteId))
        {
            return $select;
        }

        return $select->joinLeft(
            ['c_p_e' => $this->adapter->getTableName('catalog_product_entity')],
            "c_p_r.$field = c_p_e.entity_id",
            ["{$field}_type_id" => "type_id"]
        )->joinLeft(
            ['c_p_w' => $this->adapter->getTableName('catalog_product_website')],
            "c_p_r.$field = c_p_w.product_id",
            []
        )->where("c_p_w.website_id= ? " , $websiteId)
            ->where("c_p_e.entity_id IS NOT NULL");
    }


}
