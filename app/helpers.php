<?php

if (! function_exists('bba_version')) {
    function bba_version($file)
    {
        return elixir($file, 'dist');
    }
}
