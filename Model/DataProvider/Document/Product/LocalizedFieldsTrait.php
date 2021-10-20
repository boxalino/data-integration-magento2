<?php
namespace Boxalino\DataIntegration\Model\DataProvider\Document\Product;

use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;

/**
 * Trait for storing common logic for localized content access
 * Dependent on the IntegrationDocHandlerTrait
 *
 * @package Boxalino\DataIntegration\Model\DataProvider\Document\Product
 */
trait LocalizedFieldsTrait
{
    use DiIntegrationConfigurationTrait;

    /**
     * @var string
     */
    protected $prefix = "translation";

    /**
     * @return array
     * @throws \Exception
     */
    public function getFields(string $diIdFieldMap = "c_p_e.entity_id") : array
    {
        return array_merge($this->getLanguageHeaderColumns(),[new \Zend_Db_Expr("$diIdFieldMap AS {$this->getDiIdField()}")]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getLanguageHeaderColumns() : array
    {
        return preg_filter('/^/', $this->getPrefix() .'.', $this->getSystemConfiguration()->getLanguages());
    }

    /**
     * @return string
     */
    public function getPrefix() : string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }


}
