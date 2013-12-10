<?php
namespace AtansLogger\Service;

use AtansUser\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
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
    protected $entityManager;

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
     * @param  string $name
     * @param  string|null $message
     * @param  int|null $objectId
     * @param  User|null $createdBy
     * @return bool
     */
    public function log($target, $name, $message = null, $objectId = null, User $createdBy = null)
    {
        if (!$createdBy) {
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

        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();

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
    public function getEntityManager()
    {
        if (!$this->entityManager instanceof EntityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->entityManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $entityManager
     * @return Logger
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
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
