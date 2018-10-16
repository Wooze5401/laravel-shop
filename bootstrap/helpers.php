<?php
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function test()
{
    return 'OK11';
}
