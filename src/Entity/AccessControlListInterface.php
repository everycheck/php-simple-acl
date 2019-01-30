<?php
namespace EveryCheck\Acl\Entity;

interface AccessControlListInterface
{
    public function setUser($user);
    public function getUser();
    public function setEntity($entity);
    public function getEntity();
}
