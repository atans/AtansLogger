<?php
namespace AtansLogger\EventLogger;

use Doctrine\ORM\EntityManager;
use AtansLogger\Service\Logger as LoggerService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractEventLogger implements ServiceLocatorAwareInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerService
     */
    protected $loggerService;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Initialization
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
    }

    /**
     * Get entityManager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if (! $this->entityManager instanceof EntityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->entityManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $entityManager
     * @return AbstractEventLogger
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get loggerService
     *
     * @return LoggerService
     */
    public function getLoggerService()
    {
        if (! $this->loggerService instanceof LoggerSerivce) {
            $this->setLoggerService($this->getServiceLocator()->get('atanslogger_logger_service'));
        }
        return $this->loggerService;
    }

    /**
     * Set loggerService
     *
     * @param  LoggerService $loggerService
     * @return AbstractEventLogger
     */
    public function setLoggerService(LoggerService $loggerService)
    {
        $this->loggerService = $loggerService;
        return $this;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
