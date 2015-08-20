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
class TaskHistory
{
    /**
     * @var string
     */
    const SUCCESS = "success";

    /**
     * @var string
     */
    const FAILURE = "failure";

    /**
     * @var string
     */
    const SKIPPED = "skipped";

    /**
     * @var string
     */
    const ABORTED = "aborted";

    /**
     * @var string
     */
    const UNKNOWN = "unknown";

    /**
     * @var string
     */
    protected $status;

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
     * @var \DateTime
     * @ORM\Column(nullable=true)
     */
    protected $endTime;

    /**
     * @var string
     */
    protected $cronCommand;

    /**
     * @var string
     */
    protected $taskName;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $result;

    /**
     * Returns the final task status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the final task status
     *
     * @param string $status the final task status
     *
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns the timestamp when this task was created
     *
     * @return string
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Sets the timestamp when task was created
     *
     * @param string $createTime timestamp when task was created
     *
     * @return void
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }

    /**
     * Returns the task start time
     *
     * @return string
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Sets the task start time as timestamp
     *
     * @param string $startTime the timestamp when task was started
     *
     * @return void
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Returns the end timestamp
     *
     * @return string
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Sets the task end time as timestamp
     *
     * @param string $endTime timestamp when finished or aborted
     *
     * @return void
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * Returns the cron command class name
     *
     * @return string
     */
    public function getCronCommand()
    {
        return $this->cronCommand;
    }

    /**
     * Sets the corn command class name
     *
     * @param string $cronCommand the cron command class name
     *
     * @return void
     */
    public function setCronCommand($cronCommand)
    {
        $this->cronCommand = $cronCommand;
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

    /**
     * sets the task name
     *
     * @param string $taskName the task name
     *
     * @return void
     */
    public function setTaskName($taskName)
    {
        $this->taskName = $taskName;
    }

    /**
     * returns the task name
     *
     * @return string
     */
    public function getTaskName()
    {
        return $this->taskName;
    }
}
