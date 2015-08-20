<?php
namespace TechDivision\Scheduler\Domain\Service;

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
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TechDivision\Scheduler\Domain\Model\Cron;
use TechDivision\Scheduler\Domain\Model\Task;
use TechDivision\Scheduler\Domain\Model\TaskHistory;
use TechDivision\Scheduler\Domain\Factory\TaskFactory;
use TechDivision\Scheduler\Domain\Factory\TaskHistoryFactory;
use TechDivision\Scheduler\Domain\Repository\TaskRepository;
use TechDivision\Scheduler\Domain\Repository\TaskHistoryRepository;
use Cron\CronExpression;

/**
 * @Flow\Scope("singleton")
 */
class SchedulerService
{
    /**
     * date format to compare timestamps
     *
     * @var string
     */
    const DATE_FORMAT = 'Y-m-d H:i';

    /**
     * @Flow\Inject(lazy = FALSE)
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $entityManager;

    /**
     * @Flow\Inject
     * @var \TechDivision\Scheduler\Domain\Repository\CronRepository
     */
    protected $cronRepository;

    /**
     * @Flow\Inject
     * @var \TechDivision\Scheduler\Domain\Factory\CronCommandFactory
     */
    protected $cronCommandFactory;

    /**
     * @Flow\Inject
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * @Flow\Inject
     * @var TaskFactory
     */
    protected $taskFactory;

    /**
     * @Flow\Inject
     * @var TaskHistoryFactory
     */
    protected $taskHistoryFactory;

    /**
     * @Flow\Inject
     * @var TaskHistoryRepository
     */
    protected $taskHistoryRepository;

    /**
     * @var \TYPO3\Flow\Reflection\ReflectionService
     * @Flow\Inject
     */
    protected $reflectionService;

    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Log\SystemLoggerInterface
     */
    protected $systemLogger;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * adding new tasks to work list and execute task if none other job is running
     *
     * @return void
     */
    public function process()
    {
        try {
            $this->systemLogger->log(sprintf('starting cron...'), LOG_DEBUG);

            /** adds one or more tasks to list if necessary */
            $this->addTasksForCrons();

            /** execute a task in list if no other job is currently running */
            $this->executeTaskIfNoOtherIsRunning();

        } catch (\Exception $e) {
            $this->systemLogger->logException($e);
        }
    }

    /**
     * executes next task in database
     *
     * @return void
     */
    protected function executeNextTask()
    {
        $this->systemLogger->log(sprintf("trying to execute next task"), LOG_DEBUG);

        /** @var Task $task */
        $task = $this->taskRepository->findNext(Task::PENDING);

        if ($task instanceof Task) {
            if (($cronCommandClass = $this->getCronCommandClass($task->getCron()->getCronCommand())) === false) {
                $this->moveTaskToHistory($task, TaskHistory::ABORTED);
                $this->systemLogger->log(
                    sprintf(
                        "CronCommand class '%s' for Cron '%s' was not found",
                        array($task->getCron()->getCronCommand(), $task->getCron()->getName())
                    ),
                    LOG_DEBUG
                );
            } else {
                $cronCommand = $this->cronCommandFactory->create($cronCommandClass);

                $task->setStartTime(new \DateTime());
                $task->setStatus(Task::RUNNING);
                $task->setProcessId(getmypid());
                $this->taskRepository->update($task);
                $this->persistenceManager->persistAll();

                $this->systemLogger->log(sprintf("starting task for cron '%s'", $task->getCron()->getName()), LOG_DEBUG);

                $cronCommand->run($task);

                $this->systemLogger->log(sprintf("task for cron '%s' finished", $task->getCron()->getName()), LOG_DEBUG);
                $this->moveTaskToHistory($task);
            }
        } else {
            $this->systemLogger->log(sprintf("no task found"), LOG_DEBUG);
        }
    }

    /**
     * Returns the cron command class with namespace
     *
     * @param string $cronCommand the cron command
     *
     * @return string
     * @throws \Exception
     */
    protected function getCronCommandClass($cronCommand)
    {
        $classes = $this->reflectionService->getAllSubClassNamesForClass('TechDivision\Scheduler\CronCommand\CronController');

        foreach ($classes as $class) {
            $tmpConCommand = $this->stripToCronCommandName($class);
            if ($tmpConCommand === $cronCommand) {
                return $class;
            }
        }

        return false;
    }

    /**
     * checks if it's necessary to create a new task for a cron definition
     *
     * @return void
     * @throws \TYPO3\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    protected function addTasksForCrons()
    {
        $cronDefinitions = $this->cronRepository->findAll()->toArray();

        /** @var Cron $cronDefinition */
        foreach ($cronDefinitions as $cronDefinition) {
            // skipp crons which are deactivated
            if ($cronDefinition->getStatus() === Cron::DISABLED) {
                continue;
            }

            $currentTime = date(self::DATE_FORMAT);
            $cron = CronExpression::factory($cronDefinition->getCronExpression());
            $nextRun = $cron->getNextRunDate("now", 0, true)->format(self::DATE_FORMAT);

            if ($nextRun == $currentTime) {
                $this->systemLogger->log(sprintf('adding task for cron with cronCommand "%s" ', $cronDefinition->getCronCommand()), LOG_DEBUG);

                $task = $this->taskFactory->create(new \DateTime(), Task::PENDING);
                $this->taskRepository->add($task);
                $cronDefinition->addTask($task);
                $this->persistenceManager->persistAll();
            }
        }
    }

    /**
     * executes the next task if no other job is running
     *
     * @return void
     */
    protected function executeTaskIfNoOtherIsRunning()
    {
        $runningTasks = $this->taskRepository->findByStatus(Task::RUNNING)->toArray();

        $taskCount = count($runningTasks);

        if ($taskCount == 0) {
            $this->executeNextTask();

        } elseif ($taskCount == 1) {
            $task = current($runningTasks);
            $this->checkForRunningProcess($task);

        } elseif ($taskCount > 1) {
            // this is critical because it should never happen
            $this->systemLogger->log(sprintf('more than one task is currently running'), LOG_ERR);
        }
    }

    /**
     * checks if process id of given task exists / process is still running
     *
     * @param Task $task the task model
     *
     * @return void
     */
    protected function checkForRunningProcess(Task $task)
    {
        $processorId = $task->getProcessId();

        // check if process exists / process is currently running
        // this should work on all linux and bsd / mac operating systems, but definitively non on windows
        if (!posix_getpgid($processorId)) {
            // process isn't running
            $this->moveTaskToHistory($task, TaskHistory::ABORTED);

            // execute nex task
            $this->executeNextTask();
        }
    }

    /**
     * "moves" a given task to task history repository
     *
     * @param Task $task the task model
     *
     * @return void
     * @throws \TYPO3\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function moveTaskToHistory(Task $task)
    {
        $status = $task->getStatus();
        $result = $task->getResult();

        /** @var Cron $cron */
        $cron = $task->getCron();
        $cron->removeTask($task);
        $this->taskRepository->remove($task);
        $timeEnd = new \DateTime();

        $taskHistoryModel = $this->taskHistoryFactory->create(
            $cron->getCronCommand(),
            $cron->getName(),
            $task->getCreateTime(),
            $task->getStartTime(),
            $timeEnd,
            $status,
            $result
        );

        $this->taskHistoryRepository->add($taskHistoryModel);
        $this->persistenceManager->persistAll();
    }

    /**
     * Returns all cron commands as a flat array
     *
     * @return array
     */
    public function getCronCommands()
    {
        $result = array();
        $classes = $this->reflectionService->getAllSubClassNamesForClass('TechDivision\Scheduler\CronCommand\CronController');

        foreach ($classes as $class) {
            $conCommand = $this->stripToCronCommandName($class);
            if ($conCommand !== false) {
                $result[$conCommand] = $conCommand;
            }
        }

        return $result;
    }

    /**
     * transforms a cron command class with namespace into human readable title
     *
     * @param string $class full class name inclusive namespace
     *
     * @return bool|mixed|string
     */
    protected function stripToCronCommandName($class)
    {
        $cronCommand = "";
        $pos = strrpos($class, "\\");
        if ($pos !== false) {
            $cronCommand = substr($class, $pos);
            $cronCommand = str_replace("\\", "", $cronCommand);
            $cronCommand = str_replace("Controller", "", $cronCommand);
        } else {
            return false;
        }

        return $cronCommand;
    }
}
