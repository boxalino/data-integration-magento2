<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\ResourceModel;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Helper trait for accessing generic db content
 * (joins, selects, etc)
 */
trait BaseResourceTrait
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    /**
    * @param string $table
    * @return array
    * @throws NoSuchEntityException
    */
    public function getColumnsByTableName(string $table) : array
    {
        $dbConfig = $this->deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_DB);
        $select = $this->adapter->select()
            ->from(
                'INFORMATION_SCHEMA.COLUMNS',
                ['COLUMN_NAME', 'name'=>'COLUMN_NAME']
            )
            ->where('TABLE_SCHEMA=?', $dbConfig['connection']['default']['dbname'])
            ->where('TABLE_NAME=?', $this->adapter->getTableName($table));

        $columns =  $this->adapter->fetchPairs($select);
        if (empty($columns))
        {
            throw new NoSuchEntityException(__("{$table} does not exist."));
        }

        return $columns;
    }

    /**
     * @param $table
     * @return array
     */
    public function getTableContent(string $table) : array
    {
        $select = $this->adapter->select()
            ->from($table, ['*']);

        return $this->adapter->fetchAll($select);
    }

    /**
     * @param string $table
     * @param string $prefix
     * @return Select
     */
    public function appendPrefixToColumnsSelect(string $table, string $prefix) : Select
    {
        $fields = $this->getColumnsByTableName($table);
        $prefixedFields = $this->appendPrefixToList($prefix, $fields);
        
        return $this->adapter->select()
            ->from($table, array_combine($prefixedFields, $fields));
    }

    /**
     * @param string $table
     * @param string $prefix
     * @return Select
     */
    public function appendPrefixToColumnsGroupBySelect(string $table, string $prefix, string $groupBy) : Select
    {
        $fields = $this->getColumnsByTableName($table);
        $prefixedFields = $this->appendPrefixToList($prefix, $fields);

        return $this->adapter->select()
            ->from($table, array_combine($prefixedFields, $fields))
            ->group($groupBy);
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function appendPrefixToList(string $prefix, array $list) : array
    {
        return preg_filter('/^/', $prefix .'_', $list);
    }

}
