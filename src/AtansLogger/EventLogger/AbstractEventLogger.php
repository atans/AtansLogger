<?php
namespace AtansLogger\EventLogger;

use AtansLogger\Options\ModuleOptions;
use Doctrine\ORM\EntityManager;
use AtansLogger\Service\Logger as LoggerService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractEventLogger implements ServiceLocatorAwareInterface
{
    /**
     * @var EntityManager
     */
    protected $objectManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

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
    public function getObjectManager()
    {
        if (! $this->objectManager instanceof EntityManager) {
            $this->setObjectManager($this->getServiceLocator()->get($this->getOptions()->getObjectManagerName()));
        }
        return $this->objectManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $objectManager
     * @return AbstractEventLogger
     */
    public function setObjectManager(EntityManager $objectManager)
    {
        $this->objectManager = $objectManager;
        return $this;
    }

    /**
     * Get options
     *
     * @return ModuleOptions
     */
    public function getOptions()
    {
        if (! $this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceLocator()->get('atanslogger_module_options'));
        }
        return $this->options;
    }

    /**
     * Set options
     *
     * @param  ModuleOptions $options
     * @return AbstractEventLogger
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
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
