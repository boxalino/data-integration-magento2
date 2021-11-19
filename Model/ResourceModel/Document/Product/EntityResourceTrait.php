<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;

use Magento\Framework\DB\Select;

/**
 * Helper trait for accessing eav content
 * (joins, selects, etc)
 */
trait EntityResourceTrait
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @param string $websiteId
     * @return array
     */
    public function getEntityByWebsiteId(string $websiteId): array
    {
        $select = $this->adapter->select()
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

        return $this->adapter->fetchAll($select);
    }

    /**
     * @param string $websiteId
     * @return Select
     */
    public function getEntityByWebsiteIdSelect(string $websiteId): Select
    {
        $select = $this->adapter->select()
            ->from(
                ['e' => $this->adapter->getTableName('catalog_product_entity')],
                ["*"]
            )
            ->joinLeft(
                ['c_p_w' => $this->adapter->getTableName('catalog_product_website')],
                'e.entity_id = c_p_w.product_id AND c_p_w.website_id = ' . $websiteId,
                []
            );

        return $select;
    }


}
