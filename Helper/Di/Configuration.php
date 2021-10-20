<?php
namespace Boxalino\DataIntegration\Helper\Di;

use Boxalino\DataIntegrationDoc\Framework\Util\DiConfigurationInterface;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingConfigurationException;
use Boxalino\DataIntegrationDoc\Service\GcpRequestInterface;
use Boxalino\DataIntegrationDoc\Service\Util\ConfigurationDataObject;
use Magento\Store\Model\StoreManagerInterface;
use Boxalino\DataIntegration\Helper\Store\Configuration as StoreConfigurationHandler;
use Psr\Log\LoggerInterface;

/**
 * Class Configuration
 *
 * // base currency is declared per website, default currency - per store-view
 * // default country & locale is declared per store-view
 *
 * @package Boxalino\Exporter\Service\Util
 */
class Configuration implements DiConfigurationInterface
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $configurations = [];

    public function __construct(
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ){
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }


    public function getFullConfigurations(): array
    {
        $configurations = [];
        $this->initializeWebsitesConfigurations();
        foreach($this->configurations as $configuration)
        {
            $modeConfigurations = array_merge(
                $this->_getFullConfigurations($configuration["configurationHandler"]),
                $configuration
            );

            $configurations[] = new ConfigurationDataObject($modeConfigurations);
        }

        return $configurations;
    }

    /**
     * Process available
     * @throws \Exception
     */
    protected function initializeWebsitesConfigurations() : void
    {
        $websites = $this->storeManager->getWebsites();
        /** @var \Magento\Store\Api\Data\WebsiteInterface $website */
        foreach($websites as $website)
        {
            try{
                /** @var \Magento\Store\Model\Group $group */
                foreach ($website->getGroups() as $group)
                {
                    $languages = [];
                    $currencyCodes = [];
                    $storeIds = [];
                    $languagesCountryMap = [];
                    $currencyLanguagesMap = [];
                    $genericConfigurations = [];
                    $defaultGroupStore = $group->getDefaultStore();
                    if($defaultGroupStore)
                    {
                        $configurationHandler = new StoreConfigurationHandler($defaultGroupStore);
                        try{
                            $genericConfigurations = $this->_getGenericConfigurations($configurationHandler);
                        } catch (MissingConfigurationException $exception)
                        {
                            $this->logger->warning(
                                "Missing Configuration on default group store for group " .
                                $group->getCode() .": " . $exception->getMessage()
                            );
                        }
                    }

                    /** @var \Magento\Store\Model\Store $store */
                    foreach ($group->getStores() as $store)
                    {
                        try{
                            $storeConfigurationHandler = new StoreConfigurationHandler($store);
                            if($storeConfigurationHandler->isEnabled())
                            {
                                if(empty($genericConfigurations))
                                {
                                    $configurationHandler = $storeConfigurationHandler;
                                    $genericConfigurations = $this->_getGenericConfigurations($configurationHandler);
                                }

                                $locale = $store->getConfig('general/locale/code');
                                $language = explode('_', $locale)[0];

                                $languages[] = $language;
                                $storeIds[] = $store->getId();
                                $currencyCodes[] = $store->getDefaultCurrencyCode();
                                $currencyLanguagesMap[$language]=$store->getDefaultCurrencyCode();
                                $languagesCountryMap[$language] = explode("_", $locale)[1];
                            }
                        } catch (MissingConfigurationException $exception)
                        {
                            $this->logger->warning(
                                "Missing Configuration on store: " .
                                $store->getCode()  . ": " . $exception->getMessage()
                            );
                        }
                    }

                    /** none of the stores have the exporter enabled */
                    if(empty($storeIds))
                    {
                        continue;
                    }

                    $this->configurations[$genericConfigurations[DiConfigurationInterface::DI_CONFIG_ACCOUNT]] = array_merge(
                        $genericConfigurations,
                        ["websiteId"=> $website->getId()],
                        ["languages" => $languages],
                        ["storeIds" => $storeIds],
                        ["currencyCodes" => $currencyCodes],
                        ["currencyLanguagesMap" => $currencyLanguagesMap],
                        ["languagesCountryCodeMap" => $languagesCountryMap],
                        ["storeIdsLanguagesMap" => array_combine($storeIds, $languages)],
                        ["configurationHandler" => $configurationHandler]
                    );
                }
            } catch (MissingConfigurationException $exception)
            {
                $this->logger->warning("Missing Configuration: " . $exception->getMessage());
            }
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getDeltaConfigurations(): array
    {
        $configurations = [];
        $this->initializeWebsitesConfigurations();
        foreach($this->configurations as $configuration)
        {
            $modeConfigurations = array_merge(
                $this->_getDeltaConfigurations($configuration["configurationHandler"]),
                $configuration
            );

            $configurations[] = new ConfigurationDataObject($modeConfigurations);
        }

        return $configurations;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getInstantUpdateConfigurations(): array
    {
        $configurations = [];
        $this->initializeWebsitesConfigurations();
        foreach($this->configurations as $configuration)
        {
            $modeConfigurations = array_merge(
                $this->_getInstantConfigurations($configuration["configurationHandler"]),
                $configuration
            );

            $configurations[] = new ConfigurationDataObject($modeConfigurations);
        }

        return $configurations;
    }

    /**
     * Configurations specific for the full data integrations
     *
     * @param StoreConfigurationHandler $storeConfigurationHandler
     * @return array
     * @throws \Exception
     */
    protected function _getFullConfigurations(StoreConfigurationHandler $storeConfigurationHandler) : array
    {
        return [
            GcpRequestInterface::DI_REQUEST_MODE => GcpRequestInterface::GCP_MODE_FULL,
            DiConfigurationInterface::DI_CONFIG_ENDPOINT => $storeConfigurationHandler->getRestApiEndpointByMode(GcpRequestInterface::GCP_MODE_FULL),
            "allowProductSync" => $storeConfigurationHandler->getSyncStatusByModeType( GcpRequestInterface::GCP_MODE_FULL, GcpRequestInterface::GCP_TYPE_PRODUCT),
            "allowUserSync" => $storeConfigurationHandler->getSyncStatusByModeType(GcpRequestInterface::GCP_MODE_FULL, GcpRequestInterface::GCP_TYPE_USER),
            "allowOrderSync" => $storeConfigurationHandler->getSyncStatusByModeType(GcpRequestInterface::GCP_MODE_FULL, GcpRequestInterface::GCP_TYPE_ORDER),
        ];
    }

    /**
     * Configurations specific for the instant data integrations
     *
     * @param StoreConfigurationHandler $storeConfigurationHandler
     * @return array
     * @throws \Exception
     */
    protected function _getInstantConfigurations(StoreConfigurationHandler $storeConfigurationHandler) : array
    {
        return [
            GcpRequestInterface::DI_REQUEST_MODE => GcpRequestInterface::GCP_MODE_INSTANT_UPDATE,
            DiConfigurationInterface::DI_CONFIG_ENDPOINT => $storeConfigurationHandler->getRestApiEndpointByMode(GcpRequestInterface::GCP_MODE_INSTANT_UPDATE),
            "allowProductSync" => $storeConfigurationHandler->getSyncStatusByModeType(GcpRequestInterface::GCP_MODE_INSTANT_UPDATE, GcpRequestInterface::GCP_TYPE_PRODUCT),
            "allowUserSync" => $storeConfigurationHandler->getSyncStatusByModeType(GcpRequestInterface::GCP_MODE_INSTANT_UPDATE, GcpRequestInterface::GCP_TYPE_USER),
            "allowOrderSync" => $storeConfigurationHandler->getSyncStatusByModeType(GcpRequestInterface::GCP_MODE_INSTANT_UPDATE, GcpRequestInterface::GCP_TYPE_ORDER),
        ];
    }

    /**
     * Configurations specific for the delta data integrations
     *
     * @param StoreConfigurationHandler $storeConfigurationHandler
     * @return array
     * @throws \Exception
     */
    protected function _getDeltaConfigurations(StoreConfigurationHandler $storeConfigurationHandler) : array
    {
        return [
            GcpRequestInterface::DI_REQUEST_MODE => GcpRequestInterface::GCP_MODE_DELTA,
            DiConfigurationInterface::DI_CONFIG_ENDPOINT => $storeConfigurationHandler->getRestApiEndpointByMode(GcpRequestInterface::GCP_MODE_DELTA),
            "allowProductSync" => $storeConfigurationHandler->getSyncStatusByModeType(GcpRequestInterface::GCP_MODE_DELTA, GcpRequestInterface::GCP_TYPE_PRODUCT),
            "allowUserSync" => $storeConfigurationHandler->getSyncStatusByModeType(GcpRequestInterface::GCP_MODE_DELTA, GcpRequestInterface::GCP_TYPE_USER),
            "allowOrderSync" => $storeConfigurationHandler->getSyncStatusByModeType(GcpRequestInterface::GCP_MODE_DELTA, GcpRequestInterface::GCP_TYPE_ORDER),
        ];
    }


    /**
     * Generic configurations
     *
     * @param StoreConfigurationHandler $storeConfigurationHandler
     * @return array
     * @throws \Exception
     */
    protected function _getGenericConfigurations(StoreConfigurationHandler $storeConfigurationHandler) : array
    {
        return [
            DiConfigurationInterface::DI_CONFIG_ACCOUNT => $storeConfigurationHandler->getAccount(),
            DiConfigurationInterface::DI_CONFIG_IS_DEV => $storeConfigurationHandler->isDev(),
            DiConfigurationInterface::DI_CONFIG_IS_TEST => $storeConfigurationHandler->isTest(),
            DiConfigurationInterface::DI_CONFIG_API_KEY => $storeConfigurationHandler->getApiKey(),
            DiConfigurationInterface::DI_CONFIG_API_SECRET => $storeConfigurationHandler->getApiSecret(),
            "batchSize" => $storeConfigurationHandler->getBatchSize(),
            "defaultStoreId" => $storeConfigurationHandler->getStore()->getId(),
            "defaultCurrencyCode" => $storeConfigurationHandler->getStore()->getDefaultCurrencyCode(),
            "defaultLanguageCode" => explode("_", $storeConfigurationHandler->getStore()->getConfig('general/locale/code'))[0],
            "defaultStore" => $storeConfigurationHandler->getStore(),
            "rootCategoryId" => $storeConfigurationHandler->getStore()->getRootCategoryId(),
        ];
    }


}
