<?php
namespace AtansLogger\Form;

use AtansLogger\Service\Event as EventService;
use Doctrine\ORM\EntityManager;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class EventSearchForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    const TRANSLATOR_TEXT_DOMAIN = 'AtansLogger';

    /**
     * @var array
     */
    protected $entities = array(
        'Event' => 'AtansLogger\Entity\Event',
    );

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var EventService
     */
    protected $eventService;

    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('event-search-form');
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-inline');
        $this->setAttribute('role', 'form');

        $this->setServiceManager($serviceManager);
        $translator   = $this->getServiceManager()->get('Translator');

        $page = new Element\Hidden('page');
        $this->add($page);

        $createdBy = new Element\Select('createdBy');
        $createdBy->setAttributes(array(
            'class' => 'form-control',
        ))->setOptions(array(
            'empty_option' => sprintf('== %s ==', $translator->translate('Creator', static::TRANSLATOR_TEXT_DOMAIN)),
            'value_options' => $this->getEntityManager()->getRepository($this->entities['Event'])->findCreators()
        ));
        $this->add($createdBy);

        $objectId = new Element\Text('objectId');
        $objectId->setAttributes(array(
            'class' => 'form-control',
            'style' => 'width: 60px;'
        ));
        $this->add($objectId);

        $events = array();
        foreach ($this->getEventService()->getEvents() as $class => $callback) {
            $events[$class] = $class;
        }

        $target = new Element\Select('target');
        $target->setAttribute('class', 'form-control');
        $target->setOptions(array(
            'empty_option' => sprintf('== %s ==', $translator->translate('Target', static::TRANSLATOR_TEXT_DOMAIN)),
            'value_options' => $events,
        ));
        $this->add($target);

        $name = new Element\Select('name');
        $name->setAttribute('class', 'form-control');
        $name->setOptions(array(
            'empty_option' => sprintf('== %s ==', $translator->translate('Event name', static::TRANSLATOR_TEXT_DOMAIN)),
        ));
        $this->add($name);


        $size = new Element\Text('count');
        $size->setAttributes(array(
            'class' => 'form-control',
            'style' => 'width: 60px;'
        ));
        $this->add($size);

        $query = new Element\Text('query');
        $query->setAttribute('class', 'form-control');
        $this->add($query);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'page' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ),
            'objectId' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ),
            'createdBy' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ),
            'target' => array(
                'required' => false,
            ),
            'name' => array(
                'required' => false,
            ),
            'count' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ),
            'query' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
        );
    }

    /**
     * Get entityManager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if (! $this->entityManager instanceof EntityManager) {
            $this->setEntityManager($this->getServiceManager()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->entityManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $entityManager
     * @return EventSearchForm
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get serviceManager
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set serviceManager
     *
     * @param  ServiceManager $serviceManager
     * @return ErrorSearchForm
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Get eventService
     *
     * @return EventService
     */
    public function getEventService()
    {
        if (! $this->eventService instanceof EventService) {
            $this->setEventService($this->getServiceManager()->get('atanslogger_event_service'));
        }
        return $this->eventService;
    }

    /**
     * Set eventService
     *
     * @param  EventService $eventService
     * @return EventSearchForm
     */
    public function setEventService($eventService)
    {
        $this->eventService = $eventService;
        return $this;
    }
}
