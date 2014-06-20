<?php
namespace AtansLogger\Controller;

use AtansLogger\Options\ModuleOptions;
use Doctrine\ORM\EntityManagerInterface;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class ErrorController extends AbstractActionController
{
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

    /**
     * @var EntityManagerInterface
     */
    protected $objectManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    public function indexAction()
    {
        $objectManager  = $this->getObjectManager();
        $request        = $this->getRequest();
        $serviceLocator = $this->getServiceLocator();

        $queryData = array_merge(array(
            'count' => $this->getOptions()->getErrorCountPerPage(),
            'page' => 1,
        ), $request->getQuery()->toArray());

        $form = $this->getErrorSearchForm();
        $form->setData($queryData);
        $form->isValid();

        $paginator = $objectManager->getRepository($this->entities['Error'])
            ->pagination($form->getData());

        return array(
            'form'             => $form,
            'paginator'        => $paginator,
            'loggerPriorities' => $serviceLocator->get('zend_log_logger_priorities'),
        );
    }

    /**
     * Get errorSearchForm
     *
     * @returnForm
     */
    public function getErrorSearchForm()
    {
        if (! $this->errorSearchForm instanceof Form) {
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

    /**
     * Get objectManager
     *
     * @return EntityManagerInterface
     */
    public function getObjectManager()
    {
        if (! $this->objectManager instanceof EntityManagerInterface) {
            $this->setObjectManager($this->getServiceLocator()->get($this->getOptions()->getObjectManagerName()));
        }
        return $this->objectManager;
    }

    /**
     * Set objectManager
     *
     * @param  EntityManagerInterface $objectManager
     * @return ErrorController
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
     * @return ErrorController
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }
}
