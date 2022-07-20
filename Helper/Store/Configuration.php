<?php
namespace Boxalino\DataIntegration\Helper\Store;

use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingConfigurationException;
use Boxalino\DataIntegrationDoc\Service\GcpRequestInterface;
use Magento\Store\Model\Store;

/**
 * Class Configuration
 * Default configuration instance defined for the default store view
 *
 * The DI is minimally configured:
 * - account & credentials - at the level of website
 * - endpoints - at the level of website
 * - status - at the level of the store
 *
 * @package Boxalino\DataIntegration\Helper\Store
 */
class Configuration
{

    public const DATA_INTEGRATION_SECTION_ID_PREFIX = "boxalino_di";
    public const DATA_INTEGRATION_GROUP_ID_PREFIX = "di_config";

    /**
     * @var Store
     */
    protected $store;

    /**
     * @param Store $store
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * @return bool
     */
    public function isEnabled() : bool
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath("status"));
        if(empty($value))
        {
            return false;
        }

        return (bool)$value;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getAccount() : string
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath("account"));
        if(empty($value))
        {
            throw new MissingConfigurationException("Boxalino DI: BOXALINO ACCOUNT NAME has not been configured.");
        }

        return $value;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getApiKey() : string
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath("apiKey"));
        if(empty($value))
        {
            throw new MissingConfigurationException("Boxalino DI Configuration: API KEY has not been configured.");
        }

        return $value;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getApiSecret() : string
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath("apiSecret"));
        if(empty($value))
        {
            throw new MissingConfigurationException("Boxalino DI Configuration: API SECRET has not been configured.");
        }

        return $value;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isDev() : bool
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath("devIndex"));
        if(empty($value))
        {
            return false;
        }

        return (bool)$value;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isTest() : bool
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath("isTest"));
        if(empty($value))
        {
            return false;
        }

        return (bool)$value;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getBatchSize() : int
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath("batchSize"));
        if(empty($value))
        {
            return 0;
        }

        return (int)$value;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getDispatch() : int
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath(GcpRequestInterface::DI_REQUEST_DISPATCH, GcpRequestInterface::GCP_MODE_FULL));
        if(empty($value))
        {
            return 0;
        }

        return (int)$value;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function getOutsource(string $mode) : bool
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath(GcpRequestInterface::DI_REQUEST_OUTSOURCE, $mode));
        if(empty($value))
        {
            return false;
        }

        return (bool)$value;
    }

    /**
     * @return string | null
     * @throws \Exception
     */
    public function getFields(string $mode) : bool
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath(GcpRequestInterface::DI_REQUEST_FIELDS, $mode));
        if(empty($value))
        {
            return false;
        }

        return (bool)$value;
    }

    /**
     * The API endpoint depends on the data sync mode (delta, full, instant)
     *
     * @return string
     * @throws \Exception
     */
    public function getRestApiEndpointByMode(string $mode) : string
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath("endpoint", $mode));
        if(empty($value))
        {
            throw new MissingConfigurationException("Boxalino DI Configuration: BOXALINO DI $mode ENDPOINT has not been configured.");
        }

        return $value;
    }

    /**
     * Accessing the data sync status by mode (delta, full, instant) and type (product, order, customer, etc)
     *
     * @param string $mode
     * @param string $type
     * @return bool
     * @throws \Exception
     */
    public function getSyncStatusByModeType(string $mode, string $type) : bool
    {
        $value = $this->getStore()->getConfig($this->getScopeConfigPath($type, $mode));
        if(empty($value))
        {
            return false;
        }

        return (bool)$value;
    }

    /**
     * @param string $field
     * @param string|null $prefix
     * @return string
     */
    public function getScopeConfigPath(string $field, ?string $prefix = null) : string
    {
        $section = self::DATA_INTEGRATION_SECTION_ID_PREFIX . "_" . $prefix;
        if(is_null($prefix))
        {
            $section = self::DATA_INTEGRATION_SECTION_ID_PREFIX;
        }

        return implode("/", [$section, self::DATA_INTEGRATION_GROUP_ID_PREFIX, $field]);
    }

    /**
     * @return Store
     */
    public function getStore(): Store
    {
        return $this->store;
    }

    /**
     * @param Store $store
     * @return Configuration
     */
    public function setStore(Store $store): Configuration
    {
        $this->store = $store;
        return $this;
    }


}
