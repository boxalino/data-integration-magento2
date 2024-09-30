<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\UserGeneratedContent;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Entity\ProductResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\GenericResourceProvider;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Magento\Framework\DB\Select;

/**
 * Resource to expose the user reviews for products Boxalino
 */
class Review extends GenericResourceProvider
{

    use ProductResourceTrait;

    /**
     * Returns the content of the wishlist table + updated_at field for
     * @param array $storeIds
     * @param string $websiteId
     * @return Select
     */
    public function getMainSelectByStoreIdsWebsiteId(array $storeIds, string $websiteId) : Select
    {
        $productMainSelect = $this->_getProductEntityByWebsiteIdSelect($websiteId);
        $storeReviewDetailSelect = $this->adapter->select()
            ->from(
                ['r_d' => $this->adapter->getTableName('review_detail')],
                ["*"]
            )
            ->where("r_d.store_id IN (?) OR r_d.store_id = 0" , $storeIds);

        return $this->adapter->select()
            ->from(
                ['r' => $this->adapter->getTableName('review')],
                [
                    DocSchemaInterface::FIELD_ID => "r.review_id",
                    DocSchemaInterface::FIELD_CREATION => "r.created_at",
                    "r.status_id",
                    new \Zend_Db_Expr("'review' AS " . DocSchemaInterface::FIELD_TYPE)
                ]
            )->joinLeft(
                ['r_d' => new \Zend_Db_Expr("( ". $storeReviewDetailSelect->__toString() . ' )')],
                "r_d.review_id = r.review_id",
                [
                    DocSchemaInterface::FIELD_STORES => "r_d.store_id",
                    DocSchemaInterface::FIELD_PERSONA_ID => "r_d.customer_id",
                    new \Zend_Db_Expr("IF(r_d.customer_id IS NULL, 'guest', 'customer') AS " . DocSchemaInterface::FIELD_PERSONA_TYPE),
                    DocSchemaInterface::FIELD_TITLE => "r_d.title",
                    DocSchemaInterface::FIELD_DESCRIPTION => "r_d.detail",
                    "r_d.nickname"
                ]
            )->joinLeft(
                ['r_s' => $this->adapter->getTableName("review_entity")],
                'r_s.entity_id = r.entity_id',
                []
            )->joinLeft(
                ['r_st' => $this->adapter->getTableName("review_status")],
                'r_st.status_id = r.status_id',
                ['r_st.status_code']
            )->joinLeft(
                ['c_p_e' => new \Zend_Db_Expr("( ". $productMainSelect->__toString() . ' )')],
                "c_p_e.entity_id = r.entity_pk_value",
                ['c_p_e.sku', 'c_p_e.type_id']
            )->joinLeft(
                ['rating' => $this->adapter->getTableName("rating_option_vote")],
                'rating.review_id = r.review_id',
                ['rating.value', 'rating.percent']
            )
            ->where('r_s.entity_code = "'. \Magento\Review\Model\Review::ENTITY_PRODUCT_CODE . '"')
            ->where('r_d.review_id IS NOT NULL');
    }

    public function getIdPrimaryKeyField() : string
    {
        return 'r.review_id';
    }

    /**
     * @return string
     */
    public function getCreatedAtField(): string
    {
        return "r.created_at";
    }

    /**
     * @return string
     */
    public function getUpdatedAtField(): string
    {
        return "";
    }

}
