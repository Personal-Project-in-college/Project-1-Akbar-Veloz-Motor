<?php
function hasAnyRole(array $roles)
{
    return isset($_SESSION['role_name']) && in_array($_SESSION['role_name'], $roles);
}
