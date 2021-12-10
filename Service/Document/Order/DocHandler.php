<?php declare(strict_types=1);
namespace Boxalino\DataIntegration\Service\Document\Order;

use Boxalino\DataIntegration\Api\Mode\DocMviewDeltaIntegrationInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrateTrait;
use Boxalino\DataIntegration\Service\Document\DocMviewDeltaIntegrationTrait;
use Boxalino\DataIntegrationDoc\Doc\DocSchemaPropertyHandlerInterface;
use Boxalino\DataIntegrationDoc\Doc\Order;
use Boxalino\DataIntegrationDoc\Framework\Util\DiHandlerIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Framework\Util\DiIntegrationConfigurationInterface;
use Boxalino\DataIntegrationDoc\Generator\DocGeneratorInterface;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\FailSyncException;
use Boxalino\DataIntegrationDoc\Service\ErrorHandler\NoRecordsFoundException;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocHandlerInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocOrderHandlerInterface;
use Boxalino\DataIntegration\Service\Document\DiIntegrationConfigurationTrait;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\DocOrder;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationInterface;
use Boxalino\DataIntegrationDoc\Service\Integration\Doc\Mode\DocDeltaIntegrationTrait;
use Psr\Log\LoggerInterface;

/**
 * Class DocHandler
 * Generates the content for the doc_order document
 * https://boxalino.atlassian.net/wiki/spaces/BPKB/pages/252313666/doc+order
 *
 * @package Boxalino\DataIntegration\Service\Document\Order
 */
class DocHandler extends DocOrder implements
    DocOrderHandlerInterface,
    DiIntegrationConfigurationInterface,
    DiHandlerIntegrationConfigurationInterface,
    DocDeltaIntegrationInterface,
    DocMviewDeltaIntegrationInterface
{

    use DiIntegrationConfigurationTrait;
    use DocDeltaIntegrationTrait;
    use DocMviewDeltaIntegrationTrait;
    use DiIntegrateTrait;

    public function __construct(
        LoggerInterface $logger,
        array $propertyHandlers = []
    ){
        parent::__construct($logger);
        foreach($propertyHandlers as $key => $propertyHandler)
        {
            if($propertyHandler instanceof DocSchemaPropertyHandlerInterface)
            {
                $this->addPropertyHandler($propertyHandler);
            }
        }
    }

    /**
     * @return $this
     */
    protected function createDocLines() : self
    {
        try {
            $this->addSystemConfigurationOnHandlers();
            $this->generateDocData();

            foreach($this->getDocData() as $id=>$content)
            {
                /** @var Order | DocHandlerInterface | DocGeneratorInterface $doc */
                $doc = $this->getDocSchemaGenerator($content);
                $doc->setCreationTm(date("Y-m-d H:i:s"));

                $this->addDocLine($doc);
            }
        } catch (\Throwable $exception)
        {
            $this->logger->info($exception->getMessage());
        }

        return $this;
    }


}
