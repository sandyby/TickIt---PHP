<?php
function isValid($data)
{
    return isset($data) && !empty(trim($data));
}

function isString($data)
{
    return preg_match('/^[a-zA-Z0-9 .,?!-]+$/', $data);
}

function isUsername($data)
{
    return preg_match('/^[a-zA-Z0-9._]{3,16}+$/', $data);
}

function isEmail($data)
{
    return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i', $data);
}

function isPassword($data)
{
    return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&_])[A-Za-z\d@$!%*?&_]{8,}$/', $data);
}
