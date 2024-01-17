<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\UserGeneratedContent;

use Boxalino\DataIntegration\Model\DataProvider\Document\GenericDataProvider;
use Boxalino\DataIntegration\Model\ResourceModel\Document\UserGeneratedContent\Review AS ReviewResource;
use Boxalino\DataIntegration\Model\ResourceModel\Document\GenericResourceProvider;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaInterface;

/**
 * Model to expose the wishlist data to Boxalino
 * The model is provided as a sample and can be modified/extended directly in the clients` integration layer
 */
class Review extends GenericDataProvider
{

    /**
     * @param ReviewResource $resource
     */
    public function __construct(
        ReviewResource $resource
    ) {
        $this->resourceModel = $resource;
    }

    public function getProducts(array $item) : array
    {
        return [["type" => $item['type_id'], "sku" => $item['sku']]];
    }

    public function getStringOptions(array $item) : array
    {
        return [
            'nickname' => [$item['nickname']],
            'status_code' => [$item['status_code']]
        ];
    }

    public function getNumericOptions(array $item) : array
    {
        return [
            'status_id' => [$item['status_id']],
            'rating_percent' => [$item['percent']]
        ];
    }


}
