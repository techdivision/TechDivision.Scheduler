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

/**
 * @Flow\Scope("singleton")
 */
class CronCommandFactory
{
    /**
     * creates a new instance of given cronCommand
     *
     * @param string $cronCommand the cronCommand name
     *
     * @return Object
     * @throws \Exception
     */
    public function create($cronCommand)
    {
        if (class_exists($cronCommand)) {
            return new $cronCommand();
        } else {
            throw new \Exception("class '{$cronCommand}' not found");
        }
    }
}
