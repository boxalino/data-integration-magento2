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
     * @return Select
     */
    public function getEntityByRootCategoryIdSql(string $rootCategoryId) : Select
    {
        $rootCategoryIdPath = $this->getRootCategoryIdPath($rootCategoryId);
        return $this->adapter->select()
            ->from(
                $this->adapter->getTableName('catalog_category_entity'),
                ["*"]
            )
            ->where('path LIKE "'. $rootCategoryIdPath.'/%"')
            ->orWhere('entity_id IN (?)', explode("/", $rootCategoryIdPath));
    }

    /**
     * @param string $rootCategoryId
     * @param string $column
     * @return Select
     */
    public function getEntityColumnByRootCategoryIdSql(string $rootCategoryId, string $column) : Select
    {
        $rootCategoryIdPath = $this->getRootCategoryIdPath($rootCategoryId);
        return $this->adapter->select()
            ->from(
                $this->adapter->getTableName('catalog_category_entity'),
                ["entity_id", $column]
            )
            ->where('path LIKE "'. $rootCategoryIdPath.'/%"')
            ->orWhere('entity_id IN (?)', explode("/", $rootCategoryIdPath));
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
