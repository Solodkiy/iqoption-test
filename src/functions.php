<?php


/**
 * Dump and die
 * @param array $vars
 */
function dd(...$vars)
{
    call_user_func_array("dump", $vars);
    die();
}
