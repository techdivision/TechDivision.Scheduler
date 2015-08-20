<?php
namespace TechDivision\Scheduler\Dummy;

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

use TechDivision\Scheduler\CronCommand\CronController;
use TechDivision\Scheduler\Domain\Model\Task;
use TechDivision\Scheduler\Domain\Model\TaskHistory;

class DummyCronController extends CronController
{
    /**
     * a dummy cron controller implementation. You can do what ever you want here.
     *
     * @param Task $task the cron task instance
     *
     * @return void
     */
    public function run(Task $task)
    {
        $name = $task->getCron()->getName();
        $this->systemLogger->log(sprintf('inside DummyCronController: executing dummy task named "%s" ', $name), LOG_DEBUG);

        // some free text which will be shown in task history section in a later version
        $task->setResult(sprintf('dummy task execution successful!'));

        // you need to set the status here, otherwise you can not see the correct status in neos backend
        $task->setStatus(TaskHistory::SUCCESS);
    }
}
