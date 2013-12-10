<?php
namespace AtansLogger\Service;

use AtansLogger\Exception;
use AtansLogger\Callback\AbstractCallback;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Event implements ServiceLocatorAwareInterface
{
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

        foreach ($classes as $class => $callbackClass) {
            $this->addEvent($class, $callbackClass);
        }

        return $this;
    }

    /**
     * Add event
     *
     * @param  string $id
     * @param  string $eventCallbackClass
     * @return Event
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function addEvent($id, $eventCallbackClass)
    {
        if (! class_exists($id)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: %s does not exist',
                __METHOD__,
                $id
            ));
        }

        if (! class_exists($eventCallbackClass)) {
            throw new Exception\InvalidArgumentException(sprintf(
                "%s: callback class '%s' does not exist",
                __METHOD__,
                $eventCallbackClass
            ));
        }

        $callbackClass = new $eventCallbackClass($this->getServiceLocator());

        if (! $callbackClass instanceof AbstractCallback) {
            throw new Exception\InvalidArgumentException(sprintf(
                "%s: callback class '%s' should instance of Log\Callback\AbstractCallback",
                __METHOD__,
                $eventCallbackClass
            ));
        }

        $this->events[$id] = $eventCallbackClass;

        $events = $this->getEventNames($callbackClass);
        foreach ($events as $method => $event) {
            if (! is_callable(array($callbackClass, $method))) {
                throw new Exception\RuntimeException(sprintf(
                    '%s::%s is not callable',
                    $eventCallbackClass,
                    $method
                ));
            }

            $callback = $callbackClass->$method();

            if (!is_callable($callback)) {
                throw new Exception\RuntimeException(sprintf(
                    '%s::%s should return callable variable',
                    $eventCallbackClass,
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
     * @param AbstractCallback $callback
     * @return array
     */
    public  function getEventNames(AbstractCallback $callback)
    {
        $methods = get_class_methods($callback);
        $events = array();
        foreach ($methods as $method) {
            if (strtolower(substr($method, -8)) == 'callback') {
                $events[$method] = str_replace('_', '.', substr($method, 0, -8));
            }
        }

        return $events;
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