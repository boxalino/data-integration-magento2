<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegration\Api\DataProvider\GenericDocInterface;
use Boxalino\DataIntegration\Model\DataProvider\DiSchemaDataProviderInterface;

/**
 * Trait DiDataProviderTrait
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
trait DiDataProviderTrait
{
    /**
     * @var DiSchemaDataProviderInterface | GenericDocInterface
     */
    protected $dataProvider;

    /**
     * @return DiSchemaDataProviderInterface
     */
    public function getDataProvider() : DiSchemaDataProviderInterface
    {
        $this->dataProvider = $this->diSchemaDataProviderResolver->get($this->getResolverType());
        $this->dataProvider->setSystemConfiguration($this->getSystemConfiguration());
        $this->dataProvider->setHandlerIntegrateTime($this->getHandlerIntegrateTime());
        $this->dataProvider->setSyncCheck($this->getSyncCheck());
        $this->dataProvider->setMviewIds($this->getIds());
        $this->dataProvider->resolve();

        return $this->dataProvider;
    }

}
