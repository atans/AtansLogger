<?php
namespace AtansLogger\Controller;

use AtansLogger\Options\EventInterface;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;

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
     * @var EventInterface
     */
    protected $options;

    public function indexAction()
    {
        $entityManager = $this->getEntityManager();
        $request       = $this->getRequest();

        $data = array(
            'target'   => $request->getQuery('target', ''),
            'query'    => $request->getQuery('query', ''),
            'page'     => $request->getQuery('page', 1),
            'count'    => $request->getQuery('count', $this->getOptions()->getEventCountPerPage()),
        );

        $form = $this->getErrorSearchForm();
        $form->setData($data);
        $form->isValid();

        $paginator = $entityManager->getRepository($this->entities['Event'])->pagination($form->getData());

        return array(
            'form'      => $form,
            'paginator' => $paginator,
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
