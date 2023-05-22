<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel\Document;

use Magento\Framework\DB\Select;

/**
 * Trait CategoryEntityResourceTrait
 * Common category resource db selects
 */
trait CategoryEntityResourceTrait
{

    /**
     * @param string $rootCategoryId
     * @return 
     */
    public function getEntityByRootCategoryIdSql(string $rootCategoryId) : Select
    {
        $rootCategoryIdPath = $this->getRootCategoryIdPath($rootCategoryId);
        $select = $this->adapter->select()
            ->from(
                $this->adapter->getTableName('catalog_category_entity'),
                ["*"]
            )
            ->where('path LIKE "'. $rootCategoryIdPath.'/%"')
            ->orWhere('entity_id IN (?)', explode("/", $rootCategoryIdPath));

        return $select;
    }

    /**
     * @param string $rootCategoryId
     * @param string $column
     * @return array
     */
    public function getEntityColumnByRootCategoryId(string $rootCategoryId, string $column) : array
    {
        $rootCategoryIdPath = $this->getRootCategoryIdPath($rootCategoryId);
        $select = $this->adapter->select()
            ->from(
                $this->adapter->getTableName('catalog_category_entity'),
                ["entity_id", $column]
            )
            ->where('path LIKE "'. $rootCategoryIdPath.'/%"')
            ->orWhere('entity_id IN (?)', explode("/", $rootCategoryIdPath));

        return $this->adapter->fetchPairs($select);
    }
    
    /**
     * @param string $rootCategoryId
     * @return string
     */
    protected function getRootCategoryIdPath(string $rootCategoryId) : string
    {
        return  $this->adapter->fetchOne(
            $this->adapter->select()
                ->from($this->adapter->getTableName('catalog_category_entity'),['path'])
                ->where("entity_id= ?", $rootCategoryId)
        );
    }


}
