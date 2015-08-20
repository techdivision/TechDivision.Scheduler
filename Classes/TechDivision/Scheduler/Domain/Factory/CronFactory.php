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
use TechDivision\Scheduler\Domain\Model\Cron;

/**
 * @Flow\Scope("singleton")
 */
class CronFactory
{
    /**
     * creates a new cron model instance
     *
     * @param string      $name           the cron name
     * @param string      $cronCommand    the cron command
     * @param string      $cronExpression the cron expression
     * @param string|null $parameter      the cron command parameter
     *
     * @return Cron
     */
    public function create($name, $cronCommand, $cronExpression, $parameter = null)
    {
        $cron = new Cron();
        $cron->setName($name);
        $cron->setCronCommand($cronCommand);
        $cron->setParameter($parameter);
        $cron->setCronExpression($cronExpression);
        $cron->setStatus(Cron::ENABLED);

        return $cron;
    }
}
