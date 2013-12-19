<?php
namespace AtansLogger\Service;

use AtansLogger\Options\ModuleOptions;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcBase\EventManager\EventProvider;

abstract class AbstractObjectEventService extends EventProvider implements ServiceLocatorAwareInterface
{
    /**
     * @var ModuleOptions
     */
    protected $atansLoggerOptions;

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var EntityManager
     */
    protected $objectManager;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Get object manager name
     *
     * @return string
     */
    abstract public function getObjectManagerName();

    /**
     * Get atansLoggerOptions
     *
     * @return ModuleOptions
     */
    public function getAtansLoggerOptions()
    {
        if (! $this->atansLoggerOptions instanceof ModuleOptions) {
            $this->setAtansLoggerOptions($this->getServiceLocator()->get('atanslogger_module_options'));
        }
        return $this->atansLoggerOptions;
    }

    /**
     * Set atansLoggerOptions
     *
     * @param  ModuleOptions $atansLoggerOptions
     * @return AbstractObjectEventService
     */
    public function setAtansLoggerOptions(ModuleOptions $atansLoggerOptions)
    {
        $this->atansLoggerOptions = $atansLoggerOptions;
        return $this;
    }


    /**
     * Get authenticationService
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (! $this->authenticationService instanceof AuthenticationService) {
            $this->setAuthenticationService($this->getServiceLocator()->get($this->getAtansLoggerOptions()->getAuthenticationService()));
        }
        return $this->authenticationService;
    }

    /**
     * Set authenticationService
     *
     * @param  AuthenticationService $authenticationService
     * @return $this
     */
    public function setAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
        return $this;
    }

    /**
     * Get entityManager
     *
     * @return EntityManager
     */
    public function getObjectManager()
    {
        if (! $this->objectManager instanceof EntityManager) {
            $this->setObjectManager($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->objectManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $objectManager
     * @return $this
     */
    public function setObjectManager(EntityManager $objectManager)
    {
        $this->objectManager = $objectManager;
        return $this;
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

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}