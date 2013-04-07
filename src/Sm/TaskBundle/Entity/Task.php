<?php

namespace Sm\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task
 *
 * @ORM\Table(name="Task")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Sm\TaskBundle\Entity\TaskRepository")
 */
class Task
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\MinLength(
     *     limit=3,
     *     message="Description must be at least {{ limit }} characters."
     * )
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completed_at", type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_at", type="datetime", nullable=true)
     */
    private $dueAt;

    /**
     * @var \Priority
     *
     * @ORM\ManyToOne(targetEntity="Priority")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="priority_id", referencedColumnName="id")
     * })
     */
    private $priority;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Task
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set completedAt
     *
     * @param \DateTime $completedAt
     * @return Task
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt
     *
     * @return \DateTime
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set dueAt
     *
     * @param \DateTime $dueAt
     * @return Task
     */
    public function setDueAt($dueAt)
    {
        $this->dueAt = $dueAt;

        return $this;
    }

    /**
     * Get dueAt
     *
     * @return \DateTime
     */
    public function getDueAt()
    {
        return $this->dueAt;
    }

    /**
     * Set priority
     *
     * @param \Sm\TaskBundle\Entity\Priority $priority
     * @return Task
     */
    public function setPriority(\Sm\TaskBundle\Entity\Priority $priority = null)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return \Sm\TaskBundle\Entity\Priority
     */
    public function getPriority()
    {
        return $this->priority;
    }


    /** @ORM\PrePersist */
    public function setCreatedAtOnPrePersist()
    {
        $this->createdAt = new \DateTime();
    }
}