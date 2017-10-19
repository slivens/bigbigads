<?php

if (! function_exists('bba_version')) {
    function bba_version($file)
    {
        return elixir($file, 'dist');
    }
}
if (! function_exists('is_psi_agent')) {
    function is_psi_agent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'Speed Insights') !== false;
    }
}


