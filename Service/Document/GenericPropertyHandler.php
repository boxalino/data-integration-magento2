<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandler;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DiPropertyTrait;
use Boxalino\DataIntegrationDoc\Service\Flow\DiLogTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;

/**
 * Class GenericPropertyHandler for any doc_X context
 * The document handler (the class responsible to create the doc_X data structure) is looping through every DocSchemaPropertyHandler
 * and is calling the `getValues()` function
 *
 * @package Boxalino\DataIntegration\Service\Document
 */
abstract class GenericPropertyHandler extends DocSchemaPropertyHandler
    implements DiIntegrationConfigurationInterface,
    DocDeltaIntegrationInterface,
    DocInstantIntegrationInterface,
    DocMviewDeltaIntegrationInterface
{

    use DiIntegrationConfigurationTrait, DiLogTrait
    {
        DiIntegrationConfigurationTrait::getDiConfiguration insteadof DiLogTrait;
    }
    use DocDeltaIntegrationTrait;
    use DocInstantIntegrationTrait;
    use DocMviewDeltaIntegrationTrait;
    use DiPropertyTrait;


    /**
     * @return array
     */
    public function getValues(): array
    {
        if($this->filterByIds())
        {
            if($this->hasModeEnabled())
            {
                return $this->_getValues();
            }

            $this->logInfo("SKIPPED " . get_class($this));
            return [];
        }

        return $this->_getValues();
    }

    /**
     * @return array
     */
    abstract public function _getValues() : array;


}
