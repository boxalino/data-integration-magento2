<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider\Document\UserGeneratedContent;

use Boxalino\DataIntegration\Model\DataProvider\Document\GenericDataProvider;
use Boxalino\DataIntegration\Model\ResourceModel\Document\UserGeneratedContent\Review AS ReviewResource;
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

    public function getTitle(array $item): array
    {
        return $this->_getLocalizedForSingleStoreValue(DocSchemaInterface::FIELD_TITLE, $item);
    }

    public function getDescription(array $item): array
    {
        return $this->_getLocalizedForSingleStoreValue(DocSchemaInterface::FIELD_DESCRIPTION, $item);
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

    /**
     * @param string $field
     * @param array $item
     * @return array
     */
    protected function _getLocalizedForSingleStoreValue(string $field, array $item) : array
    {
        if($item[DocSchemaInterface::FIELD_STORES] == 0)
        {
            return [];
        }

        $values = [];
        foreach($this->getSystemConfiguration()->getStoreIdsLanguagesMap() as $storeId => $language)
        {
            if($storeId == $item[DocSchemaInterface::FIELD_STORES])
            {
                $values[$language] = $item[$field];
                continue;
            }

            $values[$language] = "";
        }

        return $values;
    }



}
