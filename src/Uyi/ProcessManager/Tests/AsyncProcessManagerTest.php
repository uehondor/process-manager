<?php

namespace Uyi\ProcessManager\Tests;

use Mockery as m;
use Symfony\Component\Process\Process;
use Uyi\ProcessManager\AsyncProcessManager;

class AsyncProcessManagerTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->processManager = new AsyncProcessManager();
    }

    public function testAddAndGetProcesses()
    {
        $processOne = new Process('ls -la');
        $processTwo = new Process('sleep 1');

        $this->assertEquals(0, $this->processManager->count());
        $this->assertEquals(array(), $this->processManager->getProcesses());

        $this->processManager->addProcess($processOne);
        $this->assertEquals(1, $this->processManager->count());

        $this->processManager->addProcess($processTwo);
        $this->assertEquals(2, $this->processManager->count());

        $expected = array($processOne, $processTwo);
        $this->assertSame($expected, $this->processManager->getProcesses());
    }

    public function testStartShouldAllProcessesSametime()
    {
        $processOne = $this->trainProcessToReceiveStart();
        $processTwo = $this->trainProcessToReceiveStart();

        $this->processManager
            ->addProcess($processOne)
            ->addProcess($processTwo);

        $this->processManager->start();
    }

    public function testIsRunningWhenAtleastOneProcessIsStillRunning()
    {
        $this->assertFalse($this->processManager->isRunning());

        $this->trainProcessManagerToStartProcesses($this->processManager);

        $this->assertTrue($this->processManager->isRunning());
    }

    private function trainProcessToReceiveStart($times = 1)
    {
        $process = m::mock('Symfony\Component\Process\Process');
        $process->shouldReceive('start')->times($times);
        $process->shouldReceive('isRunning')->andReturn(true);
        $process->shouldReceive('stop'); //called on __destruct

        return $process;
    }

    private function trainProcessManagerToStartProcesses($processManager)
    {
        $processManager
            ->addProcess($this->trainProcessToReceiveStart())
            ->addProcess($this->trainProcessToReceiveStart());

        $processManager->start();
    }
}
