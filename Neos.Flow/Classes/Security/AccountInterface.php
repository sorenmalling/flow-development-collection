<?php
declare(strict_types=1);

/*
 * This file is part of the Neos.Flow package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

namespace Neos\Flow\Security;

use Neos\Flow\Security\Authentication\AuthenticationProviderName;
use Neos\Flow\Security\Authentication\CredentialsSource;
use Neos\Flow\Security\Policy\Role;
use Neos\Flow\Security\Policy\RoleIdentifiers;

interface AccountInterface
{

    /**
     * @param AccountIdentifier $accountIdentifier
     * @param AuthenticationProviderName $authenticationProviderName
     * @return AccountInterface
     */
    public static function create(AccountIdentifier $accountIdentifier, AuthenticationProviderName $authenticationProviderName): AccountInterface;

    /**
     * @return AccountIdentifier
     */
    public function getAccountIdentifier(): AccountIdentifier;

    /**
     * @return AuthenticationProviderName
     */
    public function getAuthenticationProviderName(): AuthenticationProviderName;

    /**
     * @return CredentialsSource
     */
    public function getCredentialsSource(): CredentialsSource;

    /**
     * @return RoleIdentifiers
     */
    public function getRoleIdentifiers(): RoleIdentifiers;

    /**
     * @return bool
     */
    public function isActive(): bool;
}
