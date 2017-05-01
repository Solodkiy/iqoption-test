<?php
use Money\Money;


/**
 * Dump and die
 * @param array $vars
 */
function dd(...$vars)
{
    call_user_func_array("dump", $vars);
    die();
}

function m(int $cents)
{
    return new Money($cents, new \Money\Currency('USD'));

}
