<?php
namespace AtansLogger\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class ErrorController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $entities = array(
        'Error' => 'AtansLogger\Entity\Error',
    );

    /**
     * @var Form
     */
    protected $errorSearchForm;

    public function indexAction()
    {
        $entityManager = $this->getEntityManager();
        $request       = $this->getRequest();

        $data = array(
            'priority' => $request->getQuery('priority', ''),
            'query'    => $request->getQuery('query', ''),
            'page'     => $request->getQuery('page', 1),
            'size'     => $request->getQuery('size', 10),
        );

        $form = $this->getErrorSearchForm();
        $form->setData($data);
        $form->isValid();

        $paginator = $entityManager->getRepository($this->entities['Error'])->pagination($form->getData());

        return array(
            'form'             => $form,
            'paginator'        => $paginator,
            'loggerPriorities' => $this->getServiceLocator()->get('zend_log_logger_priorities'),
        );
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $entityManager
     * @return ErrorController
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

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
     * Get errorSearchForm
     *
     * @returnForm
     */
    public function getErrorSearchForm()
    {
        if (!$this->errorSearchForm instanceof Form) {
            $this->setErrorSearchForm($this->getServiceLocator()->get('atanslogger_error_search_form'));
        }
        return $this->errorSearchForm;
    }

    /**
     * Set errorSearchForm
     *
     * @param  Form $errorSearchForm
     * @return ErrorController
     */
    public function setErrorSearchForm(Form $errorSearchForm)
    {
        $this->errorSearchForm = $errorSearchForm;
        return $this;
    }
}
