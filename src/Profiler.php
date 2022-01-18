<?php

namespace Rosterbuster\Profiler;

use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * Performance Profiler class.
 */
class Profiler
{
    static private array $report = [];

    static public function reportFunction($name) : void
    {
        $callerInfo = getCallerInfo();

        // if the class does not have a container, we register the class.
        if(empty(self::$report[$callerInfo['class']])) {
            self::reportClass($callerInfo['class']);
        }

        self::$report[$callerInfo['class']]['functions'][$name] = ['start' => microtime(), 'end' => -1];
    }

    /**
     * Write down the finish time for this function. Time is stamped before the caller info is gained, to exclude that
     * processing time from the profiled time.
     *
     * @return void
     */
    static public function finishFunction($name) : void
    {
        $time = microtime();
        $callerInfo = getCallerInfo();

        self::$report[$callerInfo['class']]['functions'][$name]['end'] = $time;
    }

    static public function reportClass($className = null)
    {
        self::$report[$className ?? getCallingClass()] = ['start' => microtime(), 'end' => -1, 'functions' => []];
    }

    /**
     * Write down the finish time for this class. Time is stamped before the caller info is gained, to exclude that
     * processing time from the profiled time.
     *
     * @return void
     */
    static public function finishClass()
    {
        $time = microtime();
        self::$report[getCallingClass()]['end'] = $time;
    }


    static public function generateReport(string $storagePath) : string
    {
        $report = [];
        foreach(self::$report as $className => $class)
        {
            $classReport = [];
            if($class['end'] == -1) {
                $classDiff = 'End time was not given.';
            } else {
                $classDiff = self::getTimeDifference($class['start'], $class['end']);
            }

            $classReport['time'] = $classDiff;

            foreach($class['functions'] as $functionName => $context)
            {
                $classReport['functions'][$functionName] = self::getTimeDifference($context['start'], $context['end']);;
            }

            $report[$className] = $classReport;
        }

        $json = json_encode($report);

        file_put_contents("$storagePath.json", $json);

        return $json;
    }

    static private function getTimeDifference(string $start, string $end) : string
    {
        $start = Arr::last(explode(' ', $start));
        $end = Arr::last(explode(' ', $end));

        return Carbon::createFromTimestamp($start)->diffInSeconds(Carbon::createFromTimestamp($end));
    }
}
