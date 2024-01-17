<?php
namespace Boxalino\DataIntegration\Service\ErrorHandler;

/**
 * Class EmptyBacklogException
 * @package Boxalino\DataIntegration\Service\ErrorHandler
 */
class EmptyBacklogException extends \Error
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
