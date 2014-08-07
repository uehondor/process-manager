<?php

namespace Uyi\ProcessManager;

use Symfony\Component\Process\Process;

class AsyncProcessManager
{

    private $processes;

    public function __construct()
    {
        $this->processes = array();
    }

    public function count()
    {
        return count($this->processes);
    }

    public function addProcess(Process $process)
    {
        $this->processes[] = $process;

        return $this;
    }

    /**
     * Start all registered processes asynchronously
     */
    public function start()
    {
        foreach ($this->processes as $process) {
            $process->start();
        }
    }

    /**
     * Returns true if at least one process is running
     */
    public function isRunning()
    {
        foreach ($this->processes as $process) {
            if ($process->isRunning()) {
                return true;
            }
        }

        return false;
    }

    public function getProcesses()
    {
        return $this->processes;
    }
}
