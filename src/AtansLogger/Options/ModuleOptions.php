<?php
namespace AtansLogger\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements
    EventServiceInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * @var bool
     */
    protected $enableEventService;

    /**
     * @var array
     */
    protected $events = array();

    /**
     * Set enableEventService
     *
     * @param  bool $enableEventService
     * @return ModuleOptions
     */
    public function setEnableEventService($enableEventService)
    {
        $this->enableEventService = $enableEventService;

        return $this;
    }

    /**
     * Get enableEventService
     *
     * @return bool
     */
    public function getEnableEventService()
    {
        return $this->enableEventService;
    }

    /**
     * Set events
     *
     * @param  array $events
     * @return ModuleOptions
     */
    public function setEvents(array $events)
    {
        $this->events = $events;

        return $this;
    }

    /**
     * Get events
     *
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }
}