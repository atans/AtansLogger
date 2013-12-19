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
     * Get objectManager
     *
     * @return string
     */
    public function getObjectManager();

    /**
     * Set objectManager
     *
     * @param  string $objectManager
     * @return ModuleOptions
     */
    public function setObjectManager($objectManager);
}