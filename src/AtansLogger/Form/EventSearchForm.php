<?php
namespace AtansLogger\Form;

use AtansLogger\Service\Event as EventService;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class EventSearchForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    const TRANSLATOR_TEXT_DOMAIN = 'AtansLogger';

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
            'target' => array(
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
