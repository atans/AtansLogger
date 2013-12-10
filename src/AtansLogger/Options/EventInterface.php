<?php
namespace AtansLogger\Options;

interface EventInterface
{
    /**
     * Set event count per page
     *
     * @param  int $eventCountPerPage
     * @return ModuleOptions
     */
    public function setEventCountPerPage($eventCountPerPage);

    /**
     * Get event count per page
     *
     * @return int
     */
    public function getEventCountPerPage();

    /**
     * Set enable event service
     *
     * @param  bool $enableEventService
     * @return ModuleOptions
     */
    public function setEnableEventService($enableEventService);

    /**
     * Get enable event service
     *
     * @return bool
     */
    public function getEnableEventService();

    /**
     * Set events
     *
     * @param  array $events
     * @return ModuleOptions
     */
    public function setEvents(array $events);

    /**
     * Get events
     *
     * @return array
     */
    public function getEvents();
}

