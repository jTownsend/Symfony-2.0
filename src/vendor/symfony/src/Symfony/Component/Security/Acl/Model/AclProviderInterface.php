<?php

namespace Symfony\Component\Security\Acl\Model;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Provides a common interface for retrieving ACLs.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface AclProviderInterface
{
    /**
     * Retrieves all child object identities from the database
     *
     * @param ObjectIdentityInterface $parentOid
     * @param Boolean $directChildrenOnly
     * @return array returns an array of child 'ObjectIdentity's
     */
    function findChildren(ObjectIdentityInterface $parentOid, $directChildrenOnly = false);

    /**
     * Returns the ACL that belongs to the given object identity
     *
     * @throws AclNotFoundException when there is no ACL
     * @param ObjectIdentityInterface $oid
     * @param array $sids
     * @return AclInterface
     */
    function findAcl(ObjectIdentityInterface $oid, array $sids = array());

    /**
     * Returns the ACLs that belong to the given object identities
     *
     * @throws AclNotFoundException when we cannot find an ACL for all identities
     * @param array $oids an array of ObjectIdentityInterface implementations
     * @param array $sids an array of SecurityIdentityInterface implementations
     * @return \SplObjectStorage mapping the passed object identities to ACLs
     */
    function findAcls(array $oids, array $sids = array());
}