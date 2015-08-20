<?php
namespace TechDivision\Scheduler\CronCommand;

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
 *
 * @Flow\Scope("singleton")
 */
abstract class CronController
{
    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Log\SystemLoggerInterface
     */
    protected $systemLogger;

    /**
     * stars the cron action
     *
     * @param Task $task the cron task instance
     *
     * @return void
     */
    abstract public function run(Task $task);
}
