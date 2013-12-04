<?php
namespace AtansLogger\Form;

use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class ErrorSearchForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('log-error-search-form');
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-inline');
        $this->setAttribute('role', 'form');

        $this->setServiceManager($serviceManager);

        $page = new Element\Hidden('page');
        $this->add($page);

        $priority = new Element\Select('priority');
        $priority->setAttribute('class', 'form-control');
        $priority->setOptions(array(
            'empty_option' => 'Priority',
            'value_options' => $this->getServiceManager()->get('zend_log_logger_priorities'),
        ));
        $this->add($priority);

        $size = new Element\Text('size');
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
            'priority' => array(
                'required' => false,
            ),
            'size' => array(
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
}
