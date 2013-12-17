<?php
namespace AtansLogger\Controller;

use AtansLogger\Options\ModuleOptions;
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
     * @var ModuleOptions
     */
    protected $options;

    public function indexAction()
    {
        $objectManager = $this->objectManager($this->getOptions()->getObjectManager());
        $request       = $this->getRequest();

        $data = array(
            'priority' => $request->getQuery('priority', ''),
            'query'    => $request->getQuery('query', ''),
            'page'     => $request->getQuery('page', 1),
            'count'    => $request->getQuery('count', $this->getOptions()->getErrorCountPerPage()),
        );

        $form = $this->getErrorSearchForm();
        $form->setData($data);
        $form->isValid();

        $paginator = $objectManager->getRepository($this->entities['Error'])->pagination($form->getData());

        return array(
            'form'             => $form,
            'paginator'        => $paginator,
            'loggerPriorities' => $this->getServiceLocator()->get('zend_log_logger_priorities'),
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
