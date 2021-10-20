<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider;

use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingSchemaDataProviderDefinitionException;

/**
 * Composite class for entity data provider
 */
interface DiSchemaDataProviderResolverInterface
{

    /**
     * @param string $documentType
     * @return DiSchemaDataProviderInterface
     * @throw MissingSchemaProviderDefinitionException
     */
    public function get(string $documentType): DiSchemaDataProviderInterface;


}
