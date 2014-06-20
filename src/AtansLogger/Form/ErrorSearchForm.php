<?php
namespace AtansLogger\Form;

use AtansLogger\Module;
use Zend\Form\Element;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcBase\Form\ProvidesEventsForm;

class ErrorSearchForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Initialization
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        parent::__construct('error-search-form');
        $this->setAttribute('method', 'get');
        $this->setAttribute('class', 'form-inline');
        $this->setAttribute('role', 'form');

        $this->setServiceLocator($serviceLocator);

        $page = new Element\Hidden('page');
        $this->add($page);

        $priority = new Element\Select('priority');
        $priority->setAttribute('class', 'form-control');
        $priority->setOptions(array(
            'empty_option' => sprintf(
                '== %s ==',
                $this->getTranslator()->translate(
                    'Priority',
                    Module::TRANSLATOR_TEXT_DOMAIN
                )
            ),
            'value_options' => $serviceLocator->get('zend_log_logger_priorities'),
        ));
        $this->add($priority);

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
     * Get serviceLocator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set serviceLocator
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ErrorSearchForm
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
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
            $this->setTranslator($this->getServiceLocator()->get('Translator'));
        }
        return $this->translator;
    }

    /**
     * Set translator
     *
     * @param  TranslatorInterface $translator
     * @return ErrorSearchForm
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        return $this;
    }
}
