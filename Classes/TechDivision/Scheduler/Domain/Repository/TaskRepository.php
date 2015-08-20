<?php
namespace TechDivision\Scheduler\Domain\Repository;

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
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class TaskRepository extends Repository
{
    /**
     * Returns the next task to execute
     *
     * @param string $state the task status
     *
     * @return object
     */
    public function findNext($state)
    {
        $query = $this->createQuery();
        return $query->matching($query->equals("status", $state))
            ->setOrderings(array('createTime' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING))
            ->execute()
            ->getFirst();
    }
}
