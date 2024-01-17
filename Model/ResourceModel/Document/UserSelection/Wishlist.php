<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\UserSelection;

use Boxalino\DataIntegration\Model\ResourceModel\Document\Entity\ProductResourceTrait;
use Boxalino\DataIntegration\Model\ResourceModel\Document\GenericResourceProvider;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;
use Magento\Framework\DB\Select;

/**
 * Resource to expose the wishlist data to Boxalino
 */
class Wishlist extends GenericResourceProvider
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
        $storeWishlistItemSelect = $this->adapter->select()
            ->from(
                ['w_i' => $this->adapter->getTableName('wishlist_item')],
                ["w_i.wishlist_id", new \Zend_Db_Expr("MIN(w_i.added_at) AS created_at")]
            )
            ->where("w_i.store_id IN (?) OR w_i.store_id = 0" , $storeIds)
            ->group("w_i.wishlist_id");

        return $this->adapter->select()
            ->from(
                ['w' => $this->adapter->getTableName('wishlist')],
                [
                    DocSchemaInterface::FIELD_ID => "w.wishlist_id",
                    DocSchemaInterface::FIELD_PERSONA_ID => "w.customer_id",
                    DocSchemaInterface::FIELD_UPDATE => "w.updated_at",
                    "w.shared", "w.sharing_code",
                    new \Zend_Db_Expr("'wishlist' AS " . DocSchemaInterface::FIELD_TYPE)
                ]
            )->joinLeft(
                ['w_i' => new \Zend_Db_Expr("( ". $storeWishlistItemSelect->__toString() . ' )')],
                "w_i.wishlist_id = w.wishlist_id",
                [DocSchemaInterface::FIELD_CREATION => "w_i.created_at"]
            );
    }

    /**
     * @param array $storeIds
     * @param string $websiteId
     * @return array
     */
    public function getWishlistProductList(array $storeIds, string $websiteId) : array
    {
        $mainEntitySelect = $this->getResourceByStoreIdsWebsiteIdSelect($storeIds, $websiteId);
        $productMainSelect = $this->_getProductEntityByWebsiteIdSelect($websiteId);
        $select = $this->adapter->select()
            ->from(
                ['w_i' => $this->adapter->getTableName('wishlist_item')],
                ["*"]
            )->joinLeft(
                ['c_p_e' => new \Zend_Db_Expr("( ". $productMainSelect->__toString() . ' )')],
                "w_i.product_id = c_p_e.entity_id",
                ['c_p_e.sku', 'c_p_e.type_id']
            )->joinLeft(
                ['w' => new \Zend_Db_Expr("( ". $mainEntitySelect->__toString() . ' )')],
                "w.id = w_i.wishlist_id",
                []
            )->where("w.id IS NOT NULL");

        return $this->adapter->fetchAll($select);
    }


    public function getIdPrimaryKeyField() : string
    {
        return 'w.wishlist_id';
    }

    /**
     * @return string
     */
    public function getCreatedAtField(): string
    {
        return "w_i.created_at";
    }

    /**
     * @return string
     */
    public function getUpdatedAtField(): string
    {
        return "w.updated_at";
    }


}
