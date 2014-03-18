<?php
namespace AtansLogger\Form;

use AtansLogger\Module;
use AtansLogger\Options\ModuleOptions;
use AtansLogger\Service\Event as EventService;
use Doctrine\ORM\EntityManager;
use Zend\Form\Element;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class EventSearchForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    /**
     * @var array
     */
    protected $entities = array(
        'Event' => 'AtansLogger\Entity\Event',
    );

    /**
     * @var EventService
     */
    protected $eventService;

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var EntityManager
     */
    protected $objectManager;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Initialization
     *
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('event-search-form');
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-inline');
        $this->setAttribute('role', 'form');

        $this->setServiceManager($serviceManager);
        $translator   = $this->getTranslator();

        $page = new Element\Hidden('page');
        $this->add($page);

        $createdBy = new Element\Select('createdBy');
        $createdBy->setAttributes(array(
            'class' => 'form-control',
        ))->setOptions(array(
            'empty_option' => sprintf('== %s ==', $translator->translate('Creator', Module::TRANSLATOR_TEXT_DOMAIN)),
            'value_options' => $this->getObjectManager()->getRepository($this->entities['Event'])->findCreators(),
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
            'empty_option' => sprintf('== %s ==', $translator->translate('Target', Module::TRANSLATOR_TEXT_DOMAIN)),
            'value_options' => $events,
        ));
        $this->add($target);

        $name = new Element\Select('name');
        $name->setAttribute('class', 'form-control');
        $name->setOptions(array(
            'empty_option' => sprintf('== %s ==', $translator->translate('Event name', Module::TRANSLATOR_TEXT_DOMAIN)),
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

        $this->getEventManager()->trigger('init', $this);
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

    /**
     * Get moduleOptions
     *
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions instanceof ModuleOptions) {
            $this->setModuleOptions($this->getServiceManager()->get('atanslogger_module_options'));
        }
        return $this->moduleOptions;
    }

    /**
     * Set moduleOptions
     *
     * @param  ModuleOptions $moduleOptions
     * @return EventSearchForm
     */
    public function setModuleOptions(ModuleOptions $moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
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
            $objectManager = $this->getServiceManager()->get($this->getModuleOptions()->getObjectManagerName());
            $this->setObjectManager($objectManager);
        }
        return $this->objectManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $objectManager
     * @return EventSearchForm
     */
    public function setObjectManager(EntityManager $objectManager)
    {
        $this->objectManager = $objectManager;
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
     * Get translator
     *
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        if (! $this->translator instanceof TranslatorInterface) {
            $this->setTranslator($this->getServiceManager()->get('Translator'));
        }
        return $this->translator;
    }

    /**
     * Set translator
     *
     * @param  TranslatorInterface $translator
     * @return EventSearchForm
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        return $this;
    }
}
