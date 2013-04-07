<?php

namespace Sm\TaskBundle\Model;

use Sm\TaskBundle\Entity\Priority;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\ExecutionContext;

use Sm\TaskBundle\Entity\Task;
use Symfony\Component\Validator\Constraints as Assert;

/**
 */
class TaskModel extends AbstractModel
{
    /**
     * @Assert\NotBlank()
     * @Assert\MinLength(
     *     limit=3,
     *     message="Description must be at least {{ limit }} characters."
     * )
     */
    public $description;

    /**
     * Format YYYY-MM-DD HH:MM:SS
     *
     * @Assert\DateTime()
     */
    public $completed_at;

    /**
     * Format YYYY-MM-DD HH:MM:SS
     *
     * @Assert\DateTime()
     */
    public $due_at;

    /**
     * @Assert\NotBlank()
     *
     */
    public $priority_name;


    private $priority;
    private $em;
    private $entity;


    public function __construct(EntityManager $em, Task $task)
    {
        $this->em = $em;

        $this->id = $task->getId();
        $this->description = $task->getDescription();
        $this->due_at = $task->getDueAt();
        $this->completed_at = $task->getCompletedAt();

        if ($task->getPriority()) {
            $this->priority_name = $task->getPriority()->getName();
        }

        $this->entity = $task;
    }

    public function setPriority(Priority $priority)
    {
        $this->priority = $priority;
        $this->priority_name = $priority->getName();
    }

    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Save entity
     */
    public function save()
    {
        $this->entity->setCompletedAt($this->completed_at);
        $this->entity->setDueAt($this->due_at);
        $this->entity->setDescription($this->description);
        $this->entity->setPriority($this->priority);
        $this->entity->setCreatedAt(new \DateTime());

        $this->em->persist($this->entity);
        $this->em->flush();
    }

    /**
     * Load additional validators
     *
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new Assert\Callback(array(
            'methods' => array('isValid'),
        )));
    }


    /**
     * Check if User entity is valid
     *
     * @param ExecutionContext $context
     */
    public function isValid(ExecutionContext $context)
    {
        if ($this->priority_name) {
            $repo = $this->em->getRepository('SmTaskBundle:Priority');

            $this->priority = $repo->findOneBy(array('name' => $this->priority_name));

            if (!$this->priority) {
                $context->addViolationAtSubPath('priority', 'Priority is not valid');
            }
        }
    }
}