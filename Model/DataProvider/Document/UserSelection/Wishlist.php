<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\UserSelection;

use Boxalino\DataIntegration\Model\DataProvider\Document\GenericDataProvider;
use Boxalino\DataIntegration\Model\ResourceModel\Document\UserSelection\Wishlist AS WishlistResource;
use Boxalino\DataIntegration\Model\ResourceModel\Document\GenericResourceProvider;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Model to expose the wishlist data to Boxalino
 * The model is provided as a sample and can be modified/extended directly in the clients` integration layer
 */
class Wishlist extends GenericDataProvider
{

    /**
     * @var array
     */
    protected $wishlistItems = [];

    /**
     * @param WishlistResource $resource
     */
    public function __construct(
        WishlistResource $resource
    ) {
        $this->resourceModel = $resource;
    }

    /**
     * Load wishlist items
     * @return void
     */
    public function resolve(): void
    {
        $wishlistItems = $this->getResourceModel()->getWishlistProductList(
            $this->getSystemConfiguration()->getStoreIds(),
            $this->getSystemConfiguration()->getWebsiteId()
        );

        foreach($wishlistItems as $item)
        {
            $this->wishlistItems[$item['wishlist_id']][] = [
                'type' => $item['type_id'],
                'sku' => $item['sku']
            ];
        }
    }

    public function getStringOptions(array $item) : array
    {
        return [
            'shared' => [$item['shared']],
            'sharing_code' => [$item['sharing_code']]
        ];
    }

    /**
     * NOTE: Full DDL structure is expected
     *
     * DDL: ARRAY<STRUCT<type STRING, name STRING, product_line STRING, product_group STRING, sku STRING, value NUMERIC>>
     *
     * @param array $item [["type"=>"", "name"=>"", "product_group"=>"", "sku"=>"", "value"=>(float)],[],..]
     * @return array
     */
    public function getProducts(array $item) : array
    {
        if(isset($this->wishlistItems[$item[DocSchemaInterface::FIELD_ID]]))
        {
            return $this->wishlistItems[$item[DocSchemaInterface::FIELD_ID]];
        }

        return [];
    }



}
