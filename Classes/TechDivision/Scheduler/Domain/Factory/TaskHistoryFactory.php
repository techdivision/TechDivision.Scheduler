<?php
namespace TechDivision\Scheduler\Domain\Factory;

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
use TechDivision\Scheduler\Domain\Model\TaskHistory;
use TechDivision\Scheduler\Domain\Model\Task;

/**
 * @Flow\Scope("singleton")
 */
class TaskHistoryFactory
{

    /**
     * creates a new task history model
     *
     * @param string $cronCommand the cron command class name
     * @param string $taskName    the task name
     * @param string $createTime  the created task timestamp
     * @param string $startTime   the task start timestamp
     * @param string $endTime     the task end timestamp
     * @param string $status      the final task status
     * @param string $result      a result string (could be a error description)
     *
     * @return TaskHistory
     */
    public function create($cronCommand, $taskName, $createTime, $startTime, $endTime, $status, $result = "")
    {
        $taskHistory = new TaskHistory();
        $taskHistory->setCronCommand($cronCommand);
        $taskHistory->setTaskName($taskName);
        $taskHistory->setCreateTime($createTime);
        $taskHistory->setStartTime($startTime);
        $taskHistory->setEndTime($endTime);

        if (!$status || $status == Task::RUNNING) {
            $status = TaskHistory::UNKNOWN;
        }
        $taskHistory->setStatus($status);
        $taskHistory->setResult($result);

        return $taskHistory;
    }
}
