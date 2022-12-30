<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider;

use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;

/**
 * Interface DiSchemaDataProviderInterface
 * To be used by any property Data Provider source
 */
interface DiSchemaDataProviderInterface
    extends DiIntegrationConfigurationInterface
{

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * Preloads relevant schema data provider content
     * (optional)
     */
    public function resolve(): void;


}
