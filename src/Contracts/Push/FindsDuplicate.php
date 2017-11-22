<?php namespace Moota\SDK\Contracts\Push;


interface FindsDuplicate
{
    public function findDupes(array &$mootaInflows, array &$orders);
}
