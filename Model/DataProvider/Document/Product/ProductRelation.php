<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\ProductRelation as DataProviderResourceModel;

/**
 * Class ProductRelation
 * The following relations are available in a default M2 setup:
 * 1. relation (between products at same level, group-group, configurable-configurable, etc)
 * 2. super_link (between a variant product_id and the main product linked_product_id)
 * 3. super (between a grouped product_id and a child linked_product_id)
 * 4. up_sell (between 2 skus / product_group)
 * 5. cross_sell (between 2 skus / product_group)
 */
class ProductRelation extends ModeIntegrator
{

    /**
     * @var \ArrayObject
     */
    protected $entityIdRelationsList;

    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ) {
        $this->resourceModel = $resource;
    }

    /**
     * @return array
     */
    public function _getData(): array
    {
        return $this->entityIdRelationsList->getArrayCopy();
    }

    public function resolve(): void
    {
        $this->_resolveDataDelta();

        $this->entityIdRelationsList = new \ArrayObject();
        $this->loadSuperInformation();
        $this->loadLinkInformation();
    }

    /**
     * Loading super link information about the SKUs
     */
    protected function loadSuperInformation() : void
    {
        $this->_addRelationsByData(
            $this->getResourceModel()->getFetchAllSuperLinkByWebsiteId($this->getSystemConfiguration()->getWebsiteId())
        );
    }

    /**
     * Loading link information for the product group
     */
    protected function loadLinkInformation()
    {
        $this->_addRelationsByData(
            $this->getResourceModel()->getFetchAllLinkByWebsiteId($this->getSystemConfiguration()->getWebsiteId())
        );
    }

    /**
     * @param array $data
     */
    protected function _addRelationsByData(array $data)
    {
        foreach($data as $row)
        {
            $entityRelations = new \ArrayObject();
            if($this->entityIdRelationsList->offsetExists($row["entity_id"]))
            {
                $entityRelations = $this->entityIdRelationsList->offsetGet($row["entity_id"]);
            }

            $entityRelations->append(new \ArrayObject($row));
            $this->entityIdRelationsList->offsetSet($row['entity_id'], $entityRelations);
        }
    }


}
