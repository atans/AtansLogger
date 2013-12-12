<?php
namespace AtansLogger\Controller;

use AtansLogger\Options\EventInterface;
use AtansLogger\Service\Event as EventService;
use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EventController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $entities = array(
        'Event' => 'AtansLogger\Entity\Event',
    );

    /**
     * @var Form
     */
    protected $eventSearchForm;

    /**
     * @var EventService
     */
    protected $eventService;

    /**
     * @var EventInterface
     */
    protected $options;

    public function indexAction()
    {
        $entityManager = $this->getEntityManager();
        $request       = $this->getRequest();

        $eventRepository = $entityManager->getRepository($this->entities['Event']);

        $data = array(
            'target'    => $request->getQuery('target', ''),
            'name'      => $request->getQuery('name', ''),
            'query'     => $request->getQuery('query', ''),
            'page'      => $request->getQuery('page', 1),
            'objectId'  => $request->getQuery('objectId', ''),
            'createdBy' => $request->getQuery('createdBy', ''),
            'count'     => $request->getQuery('count', $this->getOptions()->getEventCountPerPage()),
        );

        $eventService = $this->getEventService();
        $events       = $eventService->getEvents();

        $form = $this->getEventSearchForm();
        if ($data['target'] && isset($events[$data['target']])) {
            $eventNames = $eventService->getEventNames($events[$data['target']]);
            $names = array();
            foreach ($eventNames as $eventName) {
                $names[$eventName] = $eventName;
            }

            $form->get('name')->setValueOptions($names);
        }
        $form->setData($data);
        $form->isValid();

        $paginator = $eventRepository->pagination($form->getData());


        if ($request->isXmlHttpRequest()) {
            $viewModel = new ViewModel(array(
                'isXmlHttpRequest' => true,
                'paginator'        => $paginator,
            ));
            $viewModel->setTemplate('atans-logger/event/table')
                      ->setTerminal(true);

            $viewRenderer = $this->getServiceLocator()->get('ViewRenderer');

            $table = $viewRenderer->render($viewModel);

            return $this->ajax()->success(true, array(
                'table'     => $table,
                'pageCount' => $paginator->count(),
            ));
        }

        return array(
            'isXmlHttpRequest' => false,
            'form'             => $form,
            'paginator'        => $paginator,
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
            $this->setEntityManager($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->entityManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $entityManager
     * @return EventController
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get eventSearchForm
     *
     * @return Form
     */
    public function getEventSearchForm()
    {
        if (! $this->eventSearchForm instanceof Form) {
            $this->setEventSearchForm($this->getServiceLocator()->get('atanslogger_event_search_form'));
        }
        return $this->eventSearchForm;
    }

    /**
     * Set eventSearchForm
     *
     * @param  Form $eventSearchForm
     * @return EventController
     */
    public function setEventSearchForm(Form $eventSearchForm)
    {
        $this->eventSearchForm = $eventSearchForm;
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
            $this->setEventService($this->getServiceLocator()->get('atanslogger_event_service'));
        }
        return $this->eventService;
    }

    /**
     * Set eventService
     *
     * @param  EventService $eventService
     * @return EventController
     */
    public function setEventService(EventService $eventService)
    {
        $this->eventService = $eventService;
        return $this;
    }

    /**
     * Get options
     *
     * @return EventInterface
     */
    public function getOptions()
    {
        if (! $this->options instanceof EventInterface) {
            $this->setOptions($this->getServiceLocator()->get('atanslogger_module_options'));
        }
        return $this->options;
    }

    /**
     * Set options
     *
     * @param  EventInterface $options
     * @return EventController
     */
    public function setOptions(EventInterface $options)
    {
        $this->options = $options;
        return $this;
    }
}
