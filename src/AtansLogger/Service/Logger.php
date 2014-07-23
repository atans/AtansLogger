<?php
namespace AtansLogger\Service;

use AtansUser\Entity\User;
use AtansLogger\Options\ModuleOptions;
use DateTime;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventInterface;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Logger implements ServiceLocatorAwareInterface
{
    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var EntityManager
     */
    protected $objectManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Log a event record
     *
     * @param  string $target
     * @param  string|EventInterface $name
     * @param  string|null $message
     * @param  int|null $objectId
     * @param  User|null $createdBy
     * @return bool
     */
    public function log($target, $name, $message = null, $objectId = null, User $createdBy = null)
    {
        if ($target instanceof EventInterface) {
            $target = $target->getTarget();
        }
        if (is_object($target)) {
            $target = get_class($target);
        }

        if ($name instanceof EventInterface) {
            $name = $name->getName();
        }

        if (! $createdBy) {
            $createdBy = $this->getAuthenticationService()->getIdentity();
        }

        $ipAddress = $this->getRequest()->getServer('REMOTE_ADDR');
        $username  = $createdBy ? $createdBy->getUsername() : null;

        $event = new \AtansLogger\Entity\Event();
        $event->setTarget($target)
            ->setName($name)
            ->setMessage($message)
            ->setObjectId($objectId)
            ->setCreated(new DateTime())
            ->setCreatedBy($createdBy)
            ->setIpAddress($ipAddress)
            ->setUsername($username);

        $this->getObjectManager()->persist($event);
        $this->getObjectManager()->flush();

        return true;
    }

    /**
     * Get authenticationService
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (!$this->authenticationService instanceof AuthenticationService) {
            $this->setAuthenticationService($this->getServiceLocator()->get('Zend\Authentication\AuthenticationService'));
        }
        return $this->authenticationService;
    }

    /**
     * Set authenticationService
     *
     * @param  AuthenticationService $authenticationService
     * @return Logger
     */
    public function setAuthenticationService($authenticationService)
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
        if (!$this->objectManager instanceof EntityManager) {
            $objectManager = $this->getServiceLocator()->get($this->getOptions()->getObjectManagerName());
            $this->setObjectManager($objectManager);
        }
        return $this->objectManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $entityManager
     * @return Logger
     */
    public function setObjectManager($entityManager)
    {
        $this->objectManager = $entityManager;
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
     * @return Event
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->request instanceof Request) {
            $this->setRequest($this->getServiceLocator()->get('Request'));
        }
        return $this->request;
    }

    /**
     * Set request
     *
     * @param  Request $request
     * @return Logger
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
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
