<?php
namespace Neos\Flow\Tests\Functional\Command;

/*
 * This file is part of the Neos.Flow package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

require_once(FLOW_PATH_PACKAGES . '/Framework/Neos.Flow/Tests/Behavior/Features/Bootstrap/IsolatedBehatStepsTrait.php');
require_once(FLOW_PATH_PACKAGES . '/Framework/Neos.Flow/Tests/Behavior/Features/Bootstrap/SecurityOperationsTrait.php');

use Neos\Flow\Tests\Behavior\Features\Bootstrap\IsolatedBehatStepsTrait;
use Neos\Flow\Tests\Behavior\Features\Bootstrap\SecurityOperationsTrait;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Security\Authentication\AuthenticationManagerInterface;
use Neos\Flow\Security\Authentication\Provider\TestingProvider;
use Neos\Flow\Security\Authorization\PrivilegeManagerInterface;
use Neos\Flow\Security\Context;
use Neos\Flow\Security\Policy\PolicyService;
use Neos\Flow\Utility\Environment;

/**
 * A test helper, to include behat step traits, beeing executed by
 * the BehatHelperCommandController.
 *
 * @Flow\Scope("singleton")
 */
class BehatTestHelper
{
    use IsolatedBehatStepsTrait;

    use SecurityOperationsTrait;

    /**
     * @var Bootstrap
     */
    protected static $bootstrap;

    /**
     * @var ObjectManagerInterface
     * @Flow\Inject
     */
    protected $objectManager;

    /**
     * @var Environment
     * @Flow\Inject
     */
    protected $environment;

    /**
     * @var ActionRequest
     */
    protected $mockActionRequest;

    /**
     * @var PrivilegeManagerInterface
     */
    protected $privilegeManager;

    /**
     * @var PolicyService
     * @Flow\Inject
     */
    protected $policyService;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var TestingProvider
     */
    protected $testingProvider;

    /**
     * @var Context
     */
    protected $securityContext;

    /**
     * @return void
     */
    public function initializeObject()
    {
        self::$bootstrap = Bootstrap::$staticObjectManager->get(Bootstrap::class);
        $this->isolated = false;
    }

    /**
     * @return mixed
     */
    protected function getObjectManager()
    {
        return $this->objectManager;
    }
}
