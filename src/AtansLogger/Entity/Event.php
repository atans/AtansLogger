<?php
namespace AtansLogger\Entity;

use AtansUser\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Entity(repositoryClass="EventRepository")
 * @ORM\Table(
 *   name="event_log",
 *   options={"collate"="utf8_general_ci"},
 *   indexes={@ORM\Index(name="search_index", columns={"target", "name", "object_id", "ip_address"})}
 * )
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=155)
     * @var string
     */
    protected $target;

    /**
     * @ORM\Column(type="string", length=50)
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $message;

    /**
     * @ORM\Column(name="object_id", type="integer", nullable=true)
     * @var integer
     */
    protected $objectId;

    /**
     * @ORM\Column(name="ip_address", type="string", length=20, nullable=true)
     * @var string
     */
    protected $ipAddress;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="AtansUser\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", onDelete="SET NULL")
     * @var User
     */
    protected $createdBy;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @var string
     */
    protected $username;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set target
     *
     * @param  string $target
     * @return Event
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get message
     *
     * @return array
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param  string $message
     * @return Event
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set objectId
     *
     * @param  int $objectId
     * @return Event
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
        return $this;
    }

    /**
     * Get objectId
     *
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Get ipAddress
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set ipAddress
     *
     * @param  string $ipAddress
     * @return Event
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param  DateTime $created
     * @return Event
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy
     *
     * @param  User $createdBy
     * @return Event
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param  string $username
     * @return Event
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
}
