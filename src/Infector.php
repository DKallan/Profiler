<?php

namespace Rosterbuster\Profiler;

class Infector
{
    public static function infect(string $original)
    {
        $reflection = new \ReflectionClass($original);

        $fileContent = file_get_contents($reflection->getFileName());

        preg_match("/class (?<class>[\w\_]+)/", $fileContent, $originalClass);
        $fakedContent = preg_replace("/class ([\w\_]+)/", "class $1_original", $fileContent);

        preg_match("/class (?<class>[\w\_]+)/", $fakedContent, $scrambledClass);

        $fakerCode = file_get_contents(__DIR__ . '/faker.php');

        $finishedFaker = preg_replace("/class Faker/", "class {$originalClass['class']}", $fakerCode);

        $fakedContent .= "\n\n$finishedFaker";

        $fakedContent = preg_replace("/\<\?php/", "", $fakedContent);

        return eval($fakedContent."\n return app()->makeWith({$originalClass['class']}::class, ['original' => {$scrambledClass['class']}::class]);");
    }
}