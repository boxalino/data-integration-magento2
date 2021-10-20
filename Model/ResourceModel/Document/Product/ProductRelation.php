<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document\Product;


/**
 * Class ProductRelation
 *
 * @package Boxalino\DataIntegration\Model\ResourceModel\Document\Product
 */
class ProductRelation extends ModeIntegrator
{

    /**
     * @return array
     */
    public function getSuperLinkInformation() : array
    {
        $select = $this->adapter->select()
            ->from(
                $this->adapter->getTableName('catalog_product_super_link'),
                ['entity_id' => 'product_id', 'parent_id', 'link_id']
            );
//            $select->where('product_id IN(?)', $this->exportIds);

        return $this->adapter->fetchAll($select);
    }

    /**
     * @return array
     */
    public function getLinksInformation() : array
    {
        $select = $this->adapter->select()
            ->from(
                ['pl'=> $this->adapter->getTableName('catalog_product_link')],
                ['entity_id' => 'product_id', 'linked_product_id', 'lt.code']
            )
            ->joinLeft(
                ['lt' => $this->adapter->getTableName('catalog_product_link_type')],
                'pl.link_type_id = lt.link_type_id', []
            )
            ->where('lt.link_type_id = pl.link_type_id');
//            $select->where('product_id IN(?)', $this->exportIds);

        return $this->adapter->fetchAll($select);
    }


}
