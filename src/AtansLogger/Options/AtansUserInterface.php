<?php

namespace AtansLogger\Options;

interface AtansUserInterface
{
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