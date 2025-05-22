<?php
function hashPassword($plainPassword)
{
    return password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);
}