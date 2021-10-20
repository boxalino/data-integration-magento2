<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Model\DataProvider;

use Boxalino\DataIntegrationDoc\Service\ErrorHandler\MissingSchemaDataProviderDefinitionException;

/**
 * Composite class for entity data provider
 */
class DiSchemaDataProviderResolver
    implements DiSchemaDataProviderResolverInterface
{
    /**
     * @var DiSchemaDataProviderInterface[]
     */
    private $diSchemaDataProviders;

    /**
     * @var string
     */
    private $document;

    /**
     * @param DiSchemaDataProviderInterface[] $dataProviders
     */
    public function __construct(string $document, array $dataProviders = [])
    {
        $this->document = $document;
        $this->diSchemaDataProviders = $dataProviders;
    }

    /**
     * @param string $key
     * @return DiSchemaDataProviderInterface
     */
    public function get(string $key): DiSchemaDataProviderInterface
    {
        if(isset($this->diSchemaDataProviders[$key]))
        {
            return $this->diSchemaDataProviders[$key];
        }

        throw new MissingSchemaDataProviderDefinitionException("Boxalino DI: there is no $key configured for $this->document document.");
    }


}
