<?php
namespace AtansLogger\Service;

use AtansLogger\Exception;
use AtansLogger\EventLogger\AbstractEventLogger;
use AtansLogger\Options\ModuleOptions;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Event implements ServiceLocatorAwareInterface
{
    /**
     * Event name suffix
     */
    const EVENT_SUFFIX = 'event';

    /**
     * @var array
     */
    protected $events;

    /**
     * @var EntityManagerInterface
     */
    protected $objectManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

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
        $class        = new ReflectionClass($eventClass);
        $methods      = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $filter       = new CamelCaseToSeparator('.');
        $suffixLength = strlen(static::EVENT_SUFFIX);

        $events = array();
        foreach ($methods as $method) {
            $methodName = $method->getName();
            if (strtolower(substr($methodName, -$suffixLength)) == static::EVENT_SUFFIX) {
                $events[$methodName] = strtolower($filter(substr($methodName, 0, -$suffixLength)));
            }
        }

        return $events;
    }

    /**
     * Get entityManager
     *
     * @return EntityManagerInterface
     */
    public function getObjectManager()
    {
        if (! $this->objectManager instanceof EntityManagerInterface) {
            $objectManager = $this->getServiceLocator()->get($this->getOptions()->getObjectManagerName());
            $this->setObjectManager($objectManager);
        }
        return $this->objectManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManagerInterface $objectManager
     * @return Event
     */
    public function setObjectManager(EntityManagerInterface $objectManager)
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
     * @return Event
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
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