<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Language;

use Boxalino\DataIntegrationDoc\Doc\Language;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DocGeneratorInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocLanguagesHandlerInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocLanguages;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocInstantIntegrationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Mode\DeltaIntegrationInterface;

/**
 * Class DocHandler
 *
 * Generates the content for the doc_languages document for a given account
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252280975/doc+languages
 *
 * @package Boxalino\DataIntegration\Service\Document\Language
 */
class DocHandler extends DocLanguages implements
    DocLanguagesHandlerInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface
{

    use DiIntegrationConfigurationTrait;
    use DocInstantIntegrationTrait;

    public function integrate(): void
    {
        if($this->getSystemConfiguration()->getMode()=== DeltaIntegrationInterface::INTEGRATION_MODE)
        {
            if($this->getSystemConfiguration()->getOutsource())
            {
                $this->logInfo("load for {$this->getDocType()} is outsourced.");
                return;
            }
        }

        $this->logInfo("load for {$this->getDocType()}");

        $this->createDocLines();
        parent::integrate();
    }

    /**
     * @return void
     */
    protected function createDocLines() : void
    {
        foreach($this->getSystemConfiguration()->getLanguagesCountryCodeMap() as $language => $countryCode)
        {
            /** @var Language | DocGeneratorInterface $doc */
            $doc = $this->getDocSchemaGenerator();
            $doc->setLanguage($language)
                ->setCountryCode($countryCode)
                ->setCreationTm(date("Y-m-d H:i:s"));

            $this->addDocLine($doc);
        }
    }


}
