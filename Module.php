<?php
namespace AtansLogger;

use Zend\Log\Logger;
use Zend\Mvc\MvcEvent;

class Module
{
    /**
     * Translator text domain
     */
    const TRANSLATOR_TEXT_DOMAIN = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $options = $serviceManager->get('atanslogger_module_options');

        if ($options->getEnableEventService()) {
            $eventService = $serviceManager->get('atanslogger_event_service');
            $eventService->addEvents($options->getEvents());
        }
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'atanslogger_event_service'  => 'AtansLogger\Service\Event',
                'atanslogger_logger_service' => 'AtansLogger\Service\Logger',
            ),
            'factories' => array(
                'atanslogger_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['atanslogger']) ? $config['atanslogger'] : array());
                },
                'atanslogger_error_search_form' => function ($sm) {
                    return new Form\ErrorSearchForm($sm);
                },
                'atanslogger_event_search_form' => function ($sm) {
                        return new Form\EventSearchForm($sm);
                },
                'zend_log_logger_priorities' => function ($sm) {
                    return array(
                        Logger::EMERG  => 'EMERG',
                        Logger::ALERT  => 'ALERT',
                        Logger::CRIT   => 'CRIT',
                        Logger::ERR    => 'ERR',
                        Logger::WARN   => 'WARN',
                        Logger::NOTICE => 'NOTICE',
                        Logger::INFO   => 'INFO',
                        Logger::DEBUG  => 'DEBUG',
                    );
                },
            ),
        );
    }
}