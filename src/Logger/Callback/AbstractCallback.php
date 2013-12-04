<?php
namespace AtansLogger\Callback;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use AtansLogger\Service\Logger as LoggerService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractCallback implements ServiceLocatorAwareInterface
{
    /**
     * @var DoctrineHydrator
     */
    protected $doctrineHydrator;

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

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
    }

    /**
     * Get doctrineHydrator
     *
     * @return DoctrineHydrator
     */
    public function getDoctrineHydrator()
    {
        if (! $this->doctrineHydrator instanceof DoctrineHydrator) {
            $this->setDoctrineHydrator(new DoctrineHydrator($this->getEntityManager()));
        }
        return $this->doctrineHydrator;
    }

    /**
     * Set doctrineHydrator
     *
     * @param  DoctrineHydrator $doctrineHydrator
     * @return AbstractCallback
     */
    public function setDoctrineHydrator(DoctrineHydrator $doctrineHydrator)
    {
        $this->doctrineHydrator = $doctrineHydrator;
        return $this;
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
     * @return AbstractCallback
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
     * @return AbstractCallback
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
