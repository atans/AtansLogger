<?php

namespace AtansLogger\Options;

interface AtansLoggerInterface
{
    /**
     * Set authenticationService
     *
     * @param  string $authenticationService
     * @return ModuleOptions
     */
    public function setAuthenticationService($authenticationService);

    /**
     * Get authenticationService
     *
     * @return string
     */
    public function getAuthenticationService();

    /**
     * Get objectManagerName
     *
     * @return string
     */
    public function getObjectManagerName();

    /**
     * Set objectManagerName
     *
     * @param  string $objectManagerName
     * @return ModuleOptions
     */
    public function setObjectManagerName($objectManagerName);
}