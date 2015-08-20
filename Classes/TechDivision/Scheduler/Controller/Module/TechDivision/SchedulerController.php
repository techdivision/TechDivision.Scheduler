<?php
namespace TechDivision\Scheduler\Controller\Module\TechDivision;

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
use TYPO3\Flow\Error\Message;

use TechDivision\Scheduler\Domain\Service\SchedulerService;
use TechDivision\Scheduler\Domain\Repository\CronRepository;
use TechDivision\Scheduler\Domain\Repository\TaskRepository;
use TechDivision\Scheduler\Domain\Model\Cron;
use TechDivision\Scheduler\Domain\Model\Task;
use TechDivision\Scheduler\Domain\Model\TaskHistory;
use TechDivision\Scheduler\Domain\Repository\TaskHistoryRepository;

class SchedulerController extends AbstractSchedulerController
{
    /**
     * @var \TYPO3\Flow\Reflection\ReflectionService
     * @Flow\Inject
     */
    protected $reflectionService;

    /**
     * @var SchedulerService
     * @Flow\Inject
     */
    protected $schedulerService;

    /**
     * @Flow\Inject
     * @var CronRepository
     */
    protected $cronRepository;

    /**
     * @Flow\Inject
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * @var \TechDivision\Scheduler\Domain\Factory\CronFactory
     * @Flow\Inject
     */
    protected $cronFactory;

    /**
     * @var array
     */
    protected $actionButtons;

    /**
     * @Flow\Inject
     * @var TaskHistoryRepository
     */
    protected $taskHistoryRepository;

    /**
     * class constructor
     */
    public function __construct()
    {
        $this->actionButtons = array(
            array(
                "controller" => "cron",
                "action" => "index",
                "name" => "Crons"
            ),
            array(
                "controller" => "cron",
                "action" => "taskview",
                "name" => "Tasks"
            ),
            array(
                "controller" => "cron",
                "action" => "taskhistoryview",
                "name" => "Task History"
            )
        );
    }

    /**
     * the index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->assign('crons', $this->cronRepository->findAll());

        $this->view->assign('action', "index");
        $this->view->assign('views', $this->genViewButtons());
    }

    /**
     * the new action
     *
     * @return void
     */
    public function newAction()
    {
        $this->view->assign('cronCommands', $this->schedulerService->getCronCommands());
    }

    /**
     * Creates a new cron
     *
     * @param string $name           the cron title
     * @param string $cronCommand    the cron command (e.g. "DummyCron")
     * @param string $cronExpression the cron expression as string
     * @param string $parameter      the cron command parameter
     *
     * @return void
     * @throws \TYPO3\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function createAction($name, $cronCommand, $cronExpression, $parameter = null)
    {
        if (!$this->cronExists($name)) {
            $cron = $this->cronFactory->create($name, $cronCommand, $cronExpression, $parameter);
            $this->cronRepository->add($cron);

            $this->redirect('index');
        } else {
            $this->addFlashMessage('The Cron "%s" already exists.', 'Cron already exists', Message::SEVERITY_ERROR, array($name));
            $this->unsetLastVisitedNodeAndRedirect('new');
        }
    }

    /**
     * loads the edit view for given cron
     *
     * @param Cron $cron the cron model instance
     *
     * @return void
     */
    public function editAction(Cron $cron)
    {
        $this->view->assign('cronCommands', $this->schedulerService->getCronCommands());

        $this->view->assign("cron", $cron);
        $this->setTitle($this->moduleConfiguration['label'] . ' :: ' . ucfirst($this->request->getControllerActionName()));
    }

    /**
     * job update method called by edit view
     *
     * @param Cron $cron A cron to update
     *
     * @return void
     */
    public function updateAction(Cron $cron)
    {
        // @TODO: check if cron name was changed and is unique

        $this->cronRepository->update($cron);
        $this->addFlashMessage(sprintf('The cron "%s" has been updated.', $cron->getName()));
        $this->unsetLastVisitedNodeAndRedirect('index');
    }

    /**
     * Delete a Cron.
     *
     * @param Cron $cron Cron to delete
     *
     * @Flow\IgnoreValidation("$cron")
     *
     * @return void
     */
    public function deleteAction(Cron $cron)
    {

        /** @var Task $task */
        foreach ($cron->getTasks() as $task) {
            if ($task->getStatus() === Task::RUNNING) {
                // only remove relation not the task itself
                $cron->removeTask($task);
            } else {
                // remove relation and task
                $this->taskRepository->remove($task);
            }
        }

        $this->cronRepository->remove($cron);

        $this->addFlashMessage('The Cron "%s" has been deleted.', 'Cron deleted', Message::SEVERITY_OK, array($cron->getName()));
        $this->unsetLastVisitedNodeAndRedirect('index');
    }

    /**
     * Disable a Cron.
     *
     * @param Cron $cron Cron to disable
     *
     * @Flow\IgnoreValidation("$cron")
     *
     * @return void
     */
    public function disableCronAction(Cron $cron)
    {
        $cron->setStatus(Cron::DISABLED);
        $this->cronRepository->update($cron);

        $this->addFlashMessage('The Cron "%s" has been disabled.', 'Cron disabled', Message::SEVERITY_OK, array($cron->getName()));
        $this->unsetLastVisitedNodeAndRedirect('index');
    }

    /**
     * Enable a Cron.
     *
     * @param Cron $cron Cron to enable
     *
     * @Flow\IgnoreValidation("$cron")
     *
     * @return void
     */
    public function enableCronAction(Cron $cron)
    {
        $cron->setStatus(Cron::ENABLED);
        $this->cronRepository->update($cron);

        $this->addFlashMessage('The Cron "%s" has been enabled.', 'Cron enabled', Message::SEVERITY_OK, array($cron->getName()));
        $this->unsetLastVisitedNodeAndRedirect('index');
    }

    /**
     * the task index action
     *
     * @return void
     */
    public function taskviewAction()
    {
        $this->taskRepository->setDefaultOrderings(array('createTime' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING));

        $this->view->assign('views', $this->genViewButtons());
        $this->view->assign('tasks', $this->taskRepository->findAll());
        $this->view->assign('action', "taskview");
    }

    /**
     * the task history index action
     *
     * @return void
     */
    public function taskhistoryviewAction()
    {
        $this->view->assign('action', "taskhistoryview");
        $this->view->assign('views', $this->genViewButtons());

        $this->taskHistoryRepository->setDefaultOrderings(array('createTime' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING));
        $this->view->assign('tasks', $this->taskHistoryRepository->findAll());
    }

    /**
     * skips a task
     *
     * @param Task $task the task to skip
     *
     * @Flow\IgnoreValidation("$task")
     *
     * @return void
     */
    public function skipTaskAction(Task $task)
    {
        $task->setStatus(TaskHistory::SKIPPED);
        $this->schedulerService->moveTaskToHistory($task);

        $this->addFlashMessage('The Task has been skipped.', 'Task skipped', Message::SEVERITY_OK);
        $this->unsetLastVisitedNodeAndRedirect('index');
    }

    /**
     * Returns true if a cron with given name already exists, else false
     *
     * @param string $name the cron title
     *
     * @return bool
     */
    protected function cronExists($name)
    {
        $result = $this->cronRepository->findByName($name)->toArray();

        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * generates a "collection" of stdclass objects to render the view buttons
     *
     * @return array
     */
    protected function genViewButtons()
    {
        $collection = array();
        foreach ($this->actionButtons as $actionButton) {
            $class = new \StdClass();
            foreach ($actionButton as $key => $value) {
                $class->$key = $value;
            }
            $collection[] = $class;
        }
        return $collection;
    }
}
