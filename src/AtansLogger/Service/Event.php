<?php
namespace AtansLogger\Service;

use AtansLogger\Exception;
use AtansLogger\EventLogger\AbstractEventLogger;
use DoctrineORMModule\Options\EntityManager;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Event implements ServiceLocatorAwareInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $events;

    /**
     * @var SharedEventManagerInterface
     */
    protected $sharedEventManager;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Add events
     *
     * @param array $classes
     * @return $this
     */
    public function addEvents(array $classes)
    {
        if (empty($classes)) {
            return $this;
        }

        foreach ($classes as $class => $eventLoggerClass) {
            $this->addEvent($class, $eventLoggerClass);
        }

        return $this;
    }

    /**
     * Add event
     *
     * @param  string $id
     * @param  string $eventLoggerClass
     * @return Event
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function addEvent($id, $eventLoggerClass)
    {
        if (! class_exists($id)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: %s does not exist',
                __METHOD__,
                $id
            ));
        }

        if (! class_exists($eventLoggerClass)) {
            throw new Exception\InvalidArgumentException(sprintf(
                "%s: callback class '%s' does not exist",
                __METHOD__,
                $eventLoggerClass
            ));
        }

        $eventClass = new $eventLoggerClass($this->getServiceLocator());

        if (! $eventClass instanceof AbstractEventLogger) {
            throw new Exception\InvalidArgumentException(sprintf(
                "%s:  Class '%s' should instance of AtansLogger\EventLogger\AbstractEventLogger",
                __METHOD__,
                $eventLoggerClass
            ));
        }

        $this->events[$id] = $eventLoggerClass;

        $events = $this->getEventNames($eventLoggerClass);
        foreach ($events as $method => $event) {
            if (! is_callable(array($eventClass, $method))) {
                throw new Exception\RuntimeException(sprintf(
                    '%s::%s is not callable',
                    $eventLoggerClass,
                    $method
                ));
            }

            $callback = $eventClass->$method();

            if (!is_callable($callback)) {
                throw new Exception\RuntimeException(sprintf(
                    '%s::%s should return callable variable',
                    $eventLoggerClass,
                    $method
                ));
            }

            $this->getSharedEventManager()->attach($id, $event, $callback);
        }

        return $this;
    }

    /**
     * Get events
     *
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Get events from callback class
     *
     * @param string $eventClass
     * @return array
     */
    public  function getEventNames($eventClass)
    {
        $class = new \ReflectionClass($eventClass);
        $methods = $class->getMethods();
        $events = array();
        foreach ($methods as $method) {
            $methodName = $method->getName();
            if (strtolower(substr($methodName, -5)) == 'event') {
                $events[$methodName] = str_replace('_', '.', substr($methodName, 0, -5));
            }
        }

        return $events;
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
     * @return Event
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get sharedEventManager
     *
     * @return SharedEventManagerInterface
     */
    public function getSharedEventManager()
    {
        if (!$this->sharedEventManager instanceof SharedEventManagerInterface) {
            $this->setSharedEventManager($this->getServiceLocator()->get('SharedEventManager'));
        }
        return $this->sharedEventManager;
    }

    /**
     * Set sharedEventManager
     *
     * @param  SharedEventManagerInterface$sharedEventManager
     * @return Event
     */
    public function setSharedEventManager(SharedEventManagerInterface $sharedEventManager)
    {
        $this->sharedEventManager = $sharedEventManager;
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