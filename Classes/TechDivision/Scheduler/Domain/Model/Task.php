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
class Task
{
    /**
     * @var string
     */
    const RUNNING = "running";

    /**
     * @var string
     */
    const PENDING = "pending";

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $processId;

    /**
     * The cron
     *
     * @var \TechDivision\Scheduler\Domain\Model\Cron
     * @ORM\ManyToOne(inversedBy="tasks")
     */
    protected $cron;

    /**
     * @var \DateTime
     */
    protected $createTime;

    /**
     * @var \DateTime
     * @ORM\Column(nullable=true)
     */
    protected $startTime;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $result;

    /**
     * Sets the cron model
     *
     * @param Cron $cron the cron model
     *
     * @return void
     */
    public function setCron(Cron $cron)
    {
        $this->cron = $cron;
    }

    /**
     * Returns the related cron model
     *
     * @return Cron
     */
    public function getCron()
    {
        return $this->cron;
    }

    /**
     * Sets the system process id if this task is currently running
     *
     * @param string $processId the system process id
     *
     * @return void
     */
    public function setProcessId($processId)
    {
        $this->processId = $processId;
    }

    /**
     * Returns the current task status
     *
     * @return string
     */
    public function getProcessId()
    {
        return $this->processId;
    }

    /**
     * Sets the current task status
     *
     * @param string $status the current task status
     *
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * returns the current task status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * sets the timestamp when task was created
     *
     * @param string $time timestamp
     *
     * @return void
     */
    public function setCreateTime($time)
    {
        $this->createTime = $time;
    }

    /**
     * Returns the created task timestamp
     *
     * @return string
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Returns the timestamp when task was started
     *
     * @return string
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Sets the timestamp when task was started
     *
     * @param string $started timestamp when task was started
     *
     * @return void
     */
    public function setStartTime($started)
    {
        $this->startTime = $started;
    }


    /**
     * Returns the task result
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Sets a task result
     *
     * @param string $result task result
     *
     * @return void
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
}
