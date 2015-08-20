<?php
namespace TechDivision\Scheduler\Command;

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
use TechDivision\Scheduler\Domain\Service\SchedulerService;

/**
 * @Flow\Scope("singleton")
 */
class SchedulerCommandController extends \TYPO3\Flow\Cli\CommandController
{
    /**
     * @var SchedulerService
     * @Flow\Inject
     */
    protected $schedulerService;

    /**
     * run scheduler
     *
     * @return void
     */
    public function runCommand()
    {
        /** start scheduler service */
        $this->schedulerService->process();

    }
}
