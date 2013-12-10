Atans Logger
============

Simple event logger for Zend Framework 2

## Error log

1. Create error table

```sql
CREATE TABLE IF NOT EXISTS `error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(50) NOT NULL,
  `priority` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `line` int(5) DEFAULT NULL,
  `trace` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
```

2.Add follow code to `config/autoload/global.php`

```php
    /**
     * Logger
     */
    'log' => array(
        'Zend\Log\Logger' => array(
            'errorhandler' => true,
            'exceptionhandler' => true,
            'writers' => array(
                array(
                    'name' => 'db',
                    'priority' => 1,
                    'options' => array(
                        'column' => array(
                            'timestamp' => 'date',
                            'priority'  => 'priority',
                            'message'   => 'message',
                            'extra' => array(
                                'file'  => 'file',
                                'line'  => 'line',
                                'trace' => 'trace',
                            ),
                        ),
                        'table' => 'error_log',
                        'db' => 'Zend\Db\Adapter\Adapter',
                    ),
                ),
            ),
        ),
    ),
```

3. Edit `module/Application/Module.php`

```php
class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        // code...

        // Log error
        $eventManager = $e->getApplication()->getEventManager();

        $e->getApplication()->getServiceManager()->get('Zend\Log\Logger');
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'exceptionHandler'));
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'exceptionHandler'));
    }

    public function exceptionHandler(MvcEvent $e)
    {
        if ($exception = $e->getParam('exception')) {
            $trace  = sprintf("Stack trace:\n%s\n", $exception->getTraceAsString());

            if ($previous = $exception->getPrevious()) {
                $trace .= "\nPrevious exceptions:\n\n";
                while ($previous) {
                    $trace .= sprintf(
                        "Class: %s\nFile: %s\nLine: %s\nMessage: %s\n",
                        get_class($previous),
                        $previous->getFile(),
                        $previous->getLine(),
                        $previous->getMessage()
                    );

                    $trace .= sprintf("\nStack trace:\n%s\n", $previous->getTraceAsString());
                    $previous = $previous->getPrevious();
                }
            }

            $extra = array(
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $trace,
            );

            $logger = $e->getApplication()->getServiceManager()->get('Zend\Log\Logger');
            $logger->err($exception->getMessage(), $extra);
        }
    }
```

4. Visit `http://pathtozf2/error`

## Event log

1.Create log table:

```sql
CREATE TABLE IF NOT EXISTS `event_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(155) NOT NULL,
  `name` varchar(50) NOT NULL,
  `message` longtext,
  `object_id` int(11) DEFAULT NULL,
  `ip_address` varchar(20) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F3F68C5DE12AB56` (`created_by`),
  KEY `search_index` (`target`,`name`,`object_id`,`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
```

2.Copy `atans-logger/config/atanslogger.local.php` to `config/autoload`

3.Create event callback

```php
<?php
namespace District\Logger\Callback;

use Logger\Callback\AbstractCallback;

class Group extends AbstractCallback
{
    public function message(\District\Entity\Group $group)
    {
        return implode("\n", array(
            'Id: ' . $group->getId(),
            'Name: ' . $group->getName(),
            'Created: ' . $group->getCreated()->format('Y/m/d H:i:s'),
            'Modified: ' . ($group->getModified() ? $group->getModified()->format('Y/m/d H:i:s') : ''),
            'Created By: ' . ($group->getCreatedBy() ? $group->getCreatedBy()->getUsername() : ''),
            'Modified by: ' . ($group->getModifiedBy() ? $group->getModifiedBy()->getUsername() : ''),
        ));
    }

    // add_post event same as add.post
    public function add_postCallback()
    {
        $loggerService = $this->getLoggerService();
        $self = $this;
        $callback = function ($e) use ($loggerService, $self) {
            $params = $e->getParams();
            $group = $params['group'];

            $loggerService->log(
                get_class($e->getTarget()), // Event class name
                $e->getName(), // Event name
                $self->message($group), // Message
                $group->getId() // Object id
            );
        };

        return $callback;
    }

    // edit event
    public function edit_postCallback()
    {
        return $this->add_postCallback();
    }

    // delete event
    public function deleteCallback()
    {
        return $this->add_postCallback();
    }
}
```

4.Edit `config/autoload/atanslogger.local.php`

```php
<?php

return array(
    'atanslogger' => array(
        /**
         * Enable event service
         */
        'enable_event_service' => true,

        /**
         * Events
         */
        'events' => array(
            // District Module
            'District\Service\Group'    => 'District\Logger\Callback\Group',
        ),
    ),
);
```

