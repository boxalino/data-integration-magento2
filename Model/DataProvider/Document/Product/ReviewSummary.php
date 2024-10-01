<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Model\ResourceModel\Document\DiSchemaDataProviderResourceInterface;
use Boxalino\DataIntegration\Model\ResourceModel\Document\Product\ReviewSummary as DataProviderResourceModel;

/**
 * Class ReviewSummary
 *
 * Exports the rating information (from store 0, globally available)
 * NOTE: this is provided as a sample; integration is integrators` choice.
 */
class ReviewSummary extends ModeIntegrator
{

    /**
     * @param DataProviderResourceModel | DiSchemaDataProviderResourceInterface $resource
     */
    public function __construct(
        DataProviderResourceModel $resource
    ){
        $this->resourceModel = $resource;
    }

    /**
     *
     * @return array
     */
    public function _getData() : array
    {
        return $this->getResourceModel()->getFetchAllByFieldsWebsiteId(
            [
                $this->getDiIdField() => "e.entity_id",
                $this->getAttributeCode() => "r.rating_summary"
            ],
            $this->getSystemConfiguration()->getWebsiteId()
        );
    }


}
