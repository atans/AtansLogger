<?php
namespace AtansLogger\Options;

interface ErrorInterface
{
    /**
     * Set error count per page
     *
     * @param  int $errorCountPerPage
     * @return ModuleOptions
     */
    public function setErrorCountPerPage($errorCountPerPage);

    /**
     * Get error count per page
     *
     * @return int
     */
    public function getErrorCountPerPage();
}
