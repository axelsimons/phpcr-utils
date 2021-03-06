<?php

namespace PHPCR\Tests\Util\Console\Command;

use PHPCR\RepositoryInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\HelperSet;

use PHPCR\SessionInterface;
use PHPCR\WorkspaceInterface;
use PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper;
use PHPCR\Util\Console\Helper\PhpcrHelper;

require_once(__DIR__.'/../../../Stubs/MockNode.php');
require_once(__DIR__.'/../../../Stubs/MockNodeTypeManager.php');
require_once(__DIR__.'/../../../Stubs/MockRow.php');

abstract class BaseCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var SessionInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $session;
    /** @var WorkspaceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $workspace;
    /** @var RepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $repository;
    /** @var PhpcrConsoleDumperHelper|\PHPUnit_Framework_MockObject_MockObject */
    protected $dumperHelper;
    /** @var HelperSet */
    protected $helperSet;
    /** @var Application */
    protected $application;

    public function setUp()
    {
        $this->session = $this->getMock('PHPCR\SessionInterface');
        $this->workspace = $this->getMock('PHPCR\WorkspaceInterface');
        $this->repository = $this->getMock('PHPCR\RepositoryInterface');
        $this->queryManager = $this->getMock('PHPCR\Query\QueryManagerInterface');

        $this->row1 = $this->getMock('PHPCR\Tests\Stubs\MockRow');
        $this->node1 = $this->getMock('PHPCR\Tests\Stubs\MockNode');

        $this->dumperHelper = $this->getMockBuilder(
            'PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper'
        )->disableOriginalConstructor()->getMock();

        $this->helperSet = new HelperSet(array(
            'session' => new PhpcrHelper($this->session),
            'phpcr_console_dumper' => $this->dumperHelper,
        ));

        $this->session->expects($this->any())
            ->method('getWorkspace')
            ->will($this->returnValue($this->workspace));

        $this->workspace->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->workspace->expects($this->any())
            ->method('getQueryManager')
            ->will($this->returnValue($this->queryManager));

        $this->queryManager->expects($this->any())
            ->method('getSupportedQueryLanguages')
            ->will($this->returnValue(array('JCR-SQL2')));

        $this->application = new Application();
        $this->application->setHelperSet($this->helperSet);
    }

    /**
     * Build and execute the command tester.
     *
     * @param string $name   command name
     * @param array  $args   command arguments
     * @param int    $status expected return status
     *
     * @return CommandTester
     */
    public function executeCommand($name, $args, $status = 0)
    {
        $command = $this->application->find($name);
        $commandTester = new CommandTester($command);
        $args = array_merge(array(
            'command' => $command->getName(),
        ), $args);
        $this->assertEquals(0, $commandTester->execute($args));

        return $commandTester;
    }
}
