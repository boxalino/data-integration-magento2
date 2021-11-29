<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\User;

use Boxalino\DataIntegration\Api\DataProvider\DocUserLineInterface;
use Boxalino\DataIntegration\Api\DataProvider\DocUserPropertyInterface;
use Boxalino\DataIntegration\Service\Document\BaseIntegrationPropertyHandlerAbstract;
use Boxalino\DataIntegrationDoc\Doc\DocUserAttributeTrait;

/**
 * Class IntegrationPropertyHandlerAbstract for doc_order context
 * Handles the data provider access for the export of doc_order properties and structures
 *
 * @package Boxalino\DataIntegration\Service\Document\User
 */
abstract class IntegrationPropertyHandlerAbstract extends BaseIntegrationPropertyHandlerAbstract
{

    use DocUserAttributeTrait;

    /**
     * @var DocUserPropertyInterface | DocUserLineInterface
     */
    protected $dataProvider;

    /**
     * @return DocUserPropertyInterface | DocUserLineInterface
     */
    public function getDataProvider() : DocUserPropertyInterface
    {
        if(is_null($this->dataProvider))
        {
            $this->dataProvider = $this->diSchemaDataProviderResolver->get($this->getResolverType());
            $this->dataProvider->setSystemConfiguration($this->getSystemConfiguration());
            $this->dataProvider->setHandlerIntegrateTime($this->getHandlerIntegrateTime());
            $this->dataProvider->setSyncCheck($this->getSyncCheck());
            $this->dataProvider->setMviewIds($this->getIds());

            $this->dataProvider->resolve();
        }

        return $this->dataProvider;
    }

   
    
}
