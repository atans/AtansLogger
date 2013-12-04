<?php
namespace AtansLogger\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Entity(repositoryClass="ErrorRepository")
 * @ORM\Table(
 *   name="error",
 *   options={"collate"="utf8_general_ci"}
 * )
 */
class Error
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var string
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @var string
     */
    protected $date;

    /**
     * @ORM\Column(type="integer", length=1)
     * @var int
     */
    protected $priority;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $message;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $file;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     * @var string
     */
    protected $line;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $trace;

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get line
     *
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Get trace
     *
     * @return string
     */
    public function getTrace()
    {
        return $this->trace;
    }
}