<?php
namespace AtansLogger\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements
    ErrorInterface,
    EventInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * @var int
     */
    protected $errorCountPerPage = 10;

    /**
     * @var int
     */
    protected $eventCountPerPage = 10;

    /**
     * @var bool
     */
    protected $enableEventService = true;

    /**
     * @var array
     */
    protected $events = array();

    /**
     * Set errorCountPerPage
     *
     * @param  int $errorCountPerPage
     * @return ModuleOptions
     */
    public function setErrorCountPerPage($errorCountPerPage)
    {
        $this->errorCountPerPage = (int) $errorCountPerPage;
        return $this;
    }

    /**
     * Get errorCountPerPage
     *
     * @return int
     */
    public function getErrorCountPerPage()
    {
        return $this->errorCountPerPage;
    }

    /**
     * Set eventCountPerPage
     *
     * @param  int $eventCountPerPage
     * @return ModuleOptions
     */
    public function setEventCountPerPage($eventCountPerPage)
    {
        $this->eventCountPerPage = (int) $eventCountPerPage;

        return $this;
    }

    /**
     * Get eventCountPerPage
     *
     * @return int
     */
    public function getEventCountPerPage()
    {
        return $this->eventCountPerPage;
    }

    /**
     * Set enableEventService
     *
     * @param  bool $enableEventService
     * @return ModuleOptions
     */
    public function setEnableEventService($enableEventService)
    {
        $this->enableEventService = (bool) $enableEventService;
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
