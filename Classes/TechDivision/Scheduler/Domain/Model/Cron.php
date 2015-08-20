<?php
namespace TechDivision\Scheduler\Domain\Model;

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to version 3 of the GPL license,
 * that is bundled with this package in the file LICENSE, and is
 * available online at http://www.gnu.org/licenses/gpl.txt

 * @author    Philipp Dittert <pd@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@techdivision.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/techdivision/TechDivision.Scheduler
 */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Cron
{
    /**
     * @var string
     */
    const ENABLED = true;

    /**
     * @var string
     */
    const DISABLED = false;
    /**
     * @ORM\Column(unique=true)
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $cronCommand;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $parameter;

    /**
     * @var string
     */
    protected $cronExpression;

    /**
     * @var boolean
     */
    protected $status;

    /**
     * The posts contained in this blog
     *
     * @var \Doctrine\Common\Collections\Collection<\TechDivision\Scheduler\Domain\Model\Task>
     * @ORM\OneToMany(mappedBy="cron")
     * @ORM\OrderBy({"createTime" = "DESC"})
     */
    protected $tasks;

    /**
     * model constructor
     */
    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Sets the cron command
     *
     * @param string $cronCommand the cron command
     *
     * @return void
     */
    public function setCronCommand($cronCommand)
    {
        $this->cronCommand = $cronCommand;
    }

    /**
     * Returns the cron command
     *
     * @return string
     */
    public function getCronCommand()
    {
        return $this->cronCommand;
    }

    /**
     * Sets the cron expression
     *
     * @param string $cronExpression the cron expression
     *
     * @return void
     */
    public function setCronExpression($cronExpression)
    {
        $this->cronExpression = $cronExpression;
    }

    /**
     * Returns the cron expression
     *
     * @return string
     */
    public function getCronExpression()
    {
        return $this->cronExpression;
    }

    /**
     * Sets the cron status
     *
     * @param boolean $status the cron status. true is enabled, false is disabled
     *
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns the cron status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns all related tasks
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * adds a task model to cron
     *
     * @param Task $task the task model
     *
     * @return void
     */
    public function addTask(Task $task)
    {
        $task->setCron($this);
        $this->getTasks()->add($task);
    }

    /**
     * deletes a cron - task relation
     *
     * @param Task $task the task model
     *
     * @return void
     */
    public function removeTask(Task $task)
    {
        $this->getTasks()->removeElement($task);
    }

    /**
     * Sets the cron title
     *
     * @param string $name the cron title
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the cron title
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the parameter string
     *
     * @return string
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Sets the parameter string
     *
     * @param string $parameter the parameter string
     *
     * @return void
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;
    }
}
