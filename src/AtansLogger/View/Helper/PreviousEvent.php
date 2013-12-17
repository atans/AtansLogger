<?php
namespace AtansLogger\View\Helper;

use AtansLogger\Entity\EventRepository;
use AtansLogger\Options\ModuleOptions;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

class PreviousEvent extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var array
     */
    protected $entities = array(
        'Event' => 'AtansLogger\Entity\Event',
    );

    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @var EntityManager
     */
    protected $objectManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Get previous event
     *
     * @param  string $target
     * @param  int $objectId
     * @param  int $id
     * @return \AtansLogger\Entity\Event
     */
    public function __invoke($target, $objectId, $id)
    {
        return $this->getEventRepository()->getPreviousEvent($target, $objectId, $id);
    }

    /**
     * Get eventRepository
     *
     * @return EventRepository
     */
    public function getEventRepository()
    {
        if (! $this->eventRepository instanceof EventRepository) {
            $this->setEventRepository($this->getObjectManager()->getRepository($this->entities['Event']));
        }
        return $this->eventRepository;
    }

    /**
     * Set eventRepository
     *
     * @param  EventRepository $eventRepository
     * @return PreviousEvent
     */
    public function setEventRepository(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
        return $this;
    }

    /**
     * Get objectManager
     *
     * @return EntityManager
     */
    public function getObjectManager()
    {
        if (! $this->objectManager instanceof EntityManager) {
            $this->setObjectManager($this->getServiceLocator()->get($this->getOptions()->getObjectManager()));
        }
        return $this->objectManager;
    }

    /**
     * Set objectManager
     *
     * @param  EntityManager $objectManager
     * @return PreviousEvent
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
     * @return PreviousEvent
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator->getServiceLocator();
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