<?php
namespace Neos\Flow\Tests\Functional\Security\Fixtures\Controller;

/*
 * This file is part of the Neos.Flow package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;

/**
 * A controller for functional testing
 */
class PolicyAnnotatedController extends ActionController
{
    /**
     * This method gives GRANT permission to the role "Neos.Flow.AnnotatedRole"
     *
     * @Flow\Policy(role="Neos.Flow:AnnotatedRole", permission="grant")
     */
    public function singleRoleWithGrantPermissionAction()
    {
    }

    /**
     * This method gives GRANT permission to the role "Neos.Flow.AnnotatedRole"
     *
     * @Flow\Policy(role="Neos.Flow:DeniedRole", permission="deny")
     * @Flow\Policy(role="Neos.Flow:GrantedRole", permission="grant")
     * @Flow\Policy(role="Neos.Flow:AbstainedRole", permission="abstain")
     */
    public function multipleAnnotationsWithDifferentPermissionsAction()
    {
    }

}
