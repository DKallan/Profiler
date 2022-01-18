<?php

if(!function_exists("fake")) {
    function fake(string $original)
    {
        $fileContent = file_get_contents(base_path(str_replace("\\", "/", $original).".php"));

        preg_match("/class (?<class>[\w\_]+)/", $fileContent, $originalClass);
        $fakedContent = preg_replace("/class ([\w\_]+)/", "class $1_original", $fileContent);

        preg_match("/class (?<class>[\w\_]+)/", $fakedContent, $scrambledClass);

        $fakerCode = file_get_contents(__DIR__.'/faker.php');

        $finishedFaker = preg_replace("/class Faker/", "class {$originalClass['class']}", $fakerCode);

        $fakedContent .= "\n\n$finishedFaker";

        $fakedContent = preg_replace("/\<\?php/", "", $fakedContent);

        $instancedOriginal = eval($fakedContent."\n return app()->makeWith({$originalClass['class']}::class, ['original' => {$scrambledClass['class']}::class]);");

        app()->bind($original, function () use ($instancedOriginal) {
            return $instancedOriginal;
        });
    }
}


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