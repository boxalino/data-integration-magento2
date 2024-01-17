<?php
namespace Boxalino\DataIntegration\Service\ErrorHandler;

/**
 * Class MviewViewIdNotFoundException
 * @package Boxalino\DataIntegration\Service\ErrorHandler
 */
class MviewViewIdNotFoundException extends \Error
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
