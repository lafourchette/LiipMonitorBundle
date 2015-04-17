<?php

namespace Liip\MonitorBundle\Tests;

use Liip\MonitorBundle\Runner;
use ZendDiagnostics\Runner\Reporter\ReporterInterface;
use ZendDiagnostics\Check\CheckInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Runner
     */
    protected $runner;

    public function setUp()
    {
        $this->runner = new Runner();
        $this->runner->addChecks(array_merge(array(
                'foo' => $this->createMockCheck(),
                'bar' => $this->createMockCheck(),
            ), array($this->createMockCheck()))
        );
    }

    public function checksProvider()
    {
        return array(
            array(array()),
            array(array('foo')),
            array(array('foo', 'bar')),
        );
    }

    /**
     * @dataProvider checksProvider
     */
    public function testDisableAllCheckExcept($checks)
    {
        $this->runner->disableAllChecksExcept($checks);
        $this->assertCount(count($checks), $this->runner->getChecks());
    }

    public function testInvalidValueForDisableAllCheckExcept()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->runner->disableAllChecksExcept(array('foobar'));
    }

    public function testAdditionalReporters()
    {
        $this->assertCount(0, $this->runner->getReporters());

        $this->runner->addAdditionalReporter('foo', $this->createMockReporter());
        $this->runner->addAdditionalReporter('bar', $this->createMockReporter());

        $this->assertCount(0, $this->runner->getReporters());

        $this->runner->useAdditionalReporters(array('baz'));

        $this->assertCount(0, $this->runner->getReporters());

        $this->runner->useAdditionalReporters(array('foo'));

        $this->assertCount(1, $this->runner->getReporters());

        $this->runner->useAdditionalReporters(array('bar'));

        $this->assertCount(2, $this->runner->getReporters());
    }

    public function testAdditionalReporters2()
    {
        $this->runner->addAdditionalReporter('foo', $this->createMockReporter());
        $this->runner->addAdditionalReporter('bar', $this->createMockReporter());
        $this->runner->useAdditionalReporters(array('bar', 'foo'));

        $this->assertCount(2, $this->runner->getReporters());
    }

    /**
     * @return ReporterInterface
     */
    private function createMockReporter()
    {
        return $this->getMock('ZendDiagnostics\Runner\Reporter\ReporterInterface');
    }

    /**
     * @return CheckInterface
     */
    private function createMockCheck()
    {
        return $this->getMock('ZendDiagnostics\Check\CheckInterface');
    }
}
