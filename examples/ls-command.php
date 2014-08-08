<?php

require realpath(dirname(__FILE__).'/../vendor/autoload.php');

use Symfony\Component\Process\Process;
use Uyi\ProcessManager\AsyncProcessManager;

$manager = new AsyncProcessManager();
$nProcesses = 124; // Changing this number should not change the time taken to execute all processes

for ($i = 0; $i < $nProcesses; $i++) {
    $manager->addProcess(new Process('ls -la && sleep 10'));
}

$s = microtime(true);

$manager->start();

while ($manager->isRunning()) {
    usleep(1000);
}

foreach ($manager->getProcesses() as $process) {
    printf('Process "%s" completed successfully.'.PHP_EOL, $process->getCommandline());
    echo $process->getOutput();
    echo PHP_EOL;
}

$timetaken = microtime(true) - $s;
printf('%d processes took %d seconds', $manager->count(), $timetaken);
