<?php
if(!function_exists('getCallingFunction')) {
    function getCallingFunction(bool $throughClass = false) : string
    {
        return \Illuminate\Support\Arr::last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,$throughClass ? 4 : 3))['function'];
    }
}

if(!function_exists('getCallingClass')) {
    function getCallingClass(bool $throughClass = false) : string
    {
        return \Illuminate\Support\Arr::last(explode("\\", \Illuminate\Support\Arr::last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $throughClass ? 4 : 3))['class']));
    }
}

if(!function_exists('getCallerInfo')) {
    function getCallerInfo(bool $throughClass = false) : array
    {
        $info =  \Illuminate\Support\Arr::last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $throughClass ? 4 : 3));
        $info['class'] = \Illuminate\Support\Arr::last(explode("\\", $info['class']));
        return $info;
    }
}
