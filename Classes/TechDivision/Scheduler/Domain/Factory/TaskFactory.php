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
use TechDivision\Scheduler\Domain\Model\Task;

/**
 * @Flow\Scope("singleton")
 */
class TaskFactory
{

    /**
     * creates a new task model
     *
     * @param string $timeCreated the timestamp when task was created
     * @param string $status      the task status (e.g. "pending")
     *
     * @return Task
     */
    public function create($timeCreated, $status)
    {
        $task = new Task();
        $task->setCreateTime($timeCreated);
        $task->setStatus($status);

        return $task;
    }
}
