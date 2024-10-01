<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Entity\ProductResourceTrait;
use Magento\Review\Model\Review;

/**
 * Class ReviewSummary
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class ReviewSummary extends ModeIntegrator
{

    use ProductResourceTrait;

    /**
     * @param array $fields
     * @param string $websiteId
     * @return array
     */
    public function getFetchAllByFieldsWebsiteId(array $fields, string $websiteId) : array
    {
        $productMainSelect = $this->_getProductEntityByWebsiteIdSelect($websiteId);
        $reviewSelect = $this->adapter->select()
            ->from(
                ['r_e_s' => $this->adapter->getTableName('review_entity_summary')],
                ["entity_pk_value", "rating_summary"]
            )
            ->joinLeft(
                ['r_e' => $this->adapter->getTableName('review_entity')],
            "r_e.entity_id = r_e_s.entity_type",
                []
            )
            ->where('r_e.entity_code = ?', Review::ENTITY_PRODUCT_CODE)
            ->where("r_e_s.store_id = 0");

        $select = $this->adapter->select()
            ->from(
                ["e" => new \Zend_Db_Expr("( " . $this->getEntityByWebsiteIdSelect($websiteId)->__toString() . ")")],
                $fields
            )->joinLeft(
                ["r" => new \Zend_Db_Expr("( " . $reviewSelect->__toString() . ")")],
                "e.entity_id = r.entity_pk_value",
                ["*"]
            )->where("r.rating_summary IS NOT NULL")
            ->where("e.entity_id IS NOT NULL");

        return $this->adapter->fetchAll($select);
    }


}
