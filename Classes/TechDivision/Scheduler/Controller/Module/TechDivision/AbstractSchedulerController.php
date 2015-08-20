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

/**
 * The Abstract Scheduler module controller
 *
 * @Flow\Scope("singleton")
 */
abstract class AbstractSchedulerController extends \TYPO3\Neos\Controller\Module\AbstractModuleController
{
    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Session\SessionInterface
     */
    protected $session;

    /**
     * redirects the request
     *
     * @param string  $actionName     Name of the action to forward to
     * @param string  $controllerName Unqualified object name of the controller to forward to. If not specified, the current controller is used.
     * @param string  $packageKey     Key of the package containing the controller to forward to. If not specified, the current package is assumed.
     * @param array   $arguments      Array of arguments for the target action
     * @param integer $delay          The delay in seconds. Default is no delay.
     * @param integer $statusCode     The HTTP status code for the redirect. Default is "303 See Other"
     * @param string  $format         The format to use for the redirect URI
     *
     * @return void
     */
    protected function unsetLastVisitedNodeAndRedirect($actionName, $controllerName = null, $packageKey = null, array $arguments = null, $delay = 0, $statusCode = 303, $format = null)
    {
        $this->session->putData('lastVisitedNode', null);
        parent::redirect($actionName, $controllerName, $packageKey, $arguments, $delay, $statusCode, $format);
    }
}
