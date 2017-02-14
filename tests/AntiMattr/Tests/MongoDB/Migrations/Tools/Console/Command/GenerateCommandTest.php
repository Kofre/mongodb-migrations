<?php

namespace AntiMattr\Tests\MongoDB\Migrations\Tools\Console\Command;

use AntiMattr\MongoDB\Migrations\Configuration\Configuration;
use AntiMattr\MongoDB\Migrations\Tools\Console\Command\GenerateCommand;
use AntiMattr\TestCase\AntiMattrTestCase;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Ryan Catlin <ryan.catlin@gmail.com>
 */
class GenerateCommandTest extends AntiMattrTestCase
{
    private $command;
    private $output;
    private $config;

    protected function setUp()
    {
        $this->command = new GenerateCommandStub();
        $this->output = $this->buildMock('Symfony\Component\Console\Output\OutputInterface');
        $this->config = $this->buildMock('AntiMattr\MongoDB\Migrations\Configuration\Configuration');

        $this->command->setMigrationConfiguration($this->config);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExecuteWithInvalidMigrationDirectory()
    {
        $migrationsNamespace = 'migrations-namespace';
        $migrationsDirectory = 'missing-directory';

        $root = vfsStream::setup('Base');

        $input = new ArgvInput(
            array(
                GenerateCommand::NAME
            )
        );

        // Expectations
        $this->config->expects($this->once())
            ->method('getMigrationsNamespace')
            ->will(
                $this->returnValue($migrationsNamespace)
            )
        ;
        $this->config->expects($this->once())
            ->method('getMigrationsDirectory')
            ->will(
                $this->returnValue(
                    sprintf('%s/%s',
                        vfsStream::url('Base'),
                        $migrationsDirectory
                    )
                )
            )
        ;

        // Run command, run.
        $this->command->run(
            $input,
            $this->output
        );
    }

    public function testExecute()
    {
        $migrationsNamespace = 'migrations-namespace';
        $migrationsDirectory = 'Base/Migrations';
        $versionString = '1234567890';

        $this->command->setVersionString($versionString);

        $root = vfsStream::setup(
            'Base', // rootDir
            null,   // permissions
            array(  // structure
                'Migrations' => array()
            )
        );

        $input = new ArgvInput(
            array(
                GenerateCommand::NAME
            )
        );

        // Expectations
        $this->config->expects($this->once())
            ->method('getMigrationsNamespace')
            ->will(
                $this->returnValue($migrationsNamespace)
            )
        ;
        $this->config->expects($this->once())
            ->method('getMigrationsDirectory')
            ->will(
                $this->returnValue(vfsStream::url($migrationsDirectory))
            )
        ;

        $this->command->run(
            $input,
            $this->output
        );

        // Assertions
        $filename = sprintf(
            '%s/Version%s.php',
            $migrationsDirectory,
            $versionString
        );
        //        var_dump(file_get_contents($root->getChild($filename))); exit;
        var_dump(($root->getChild($filename)->getContent())); exit;
        $this->assertTrue($root->hasChild($filename));
    }

    public function testExecuteBasedInMigrationTemplate()
    {
        $migrationsNamespace = 'migrations-namespace';
        $migrationsDirectory = 'Base/Migrations';
        $versionString = '1234567890';

        $this->command->setVersionString($versionString);

        $root = vfsStream::setup(
            'Base', // rootDir
            null,   // permissions
            array(  // structure
                'Migrations' => array()
            )
        );


        $input = new ArgvInput(
            array(
                GenerateCommand::NAME,
                "--up-template={$this->command->getUp()}",
                "--down-template={$this->command->getDown()}"
            )
//                "down-template:{$this->command->getDown()}"]
        );
//        $input->setOption('up-template',$this->command->getUp());
//        $input->setOption('down-template',$this->command->getDown());
//        var_dump($input); exit();

        // Expectations
        $this->config->expects($this->once())
            ->method('getMigrationsNamespace')
            ->will(
                $this->returnValue($migrationsNamespace)
            )
        ;
        $this->config->expects($this->once())
            ->method('getMigrationsDirectory')
            ->will(
                $this->returnValue(vfsStream::url($migrationsDirectory))
            )
        ;

                        var_dump($root); exit();
        $this->command->run(
            $input,
            $this->output
        );

        // Assertions
        $filename = sprintf(
            '%s/Version%s.php',
            $migrationsDirectory,
            $versionString
        );
        //        var_dump(file_get_contents($root->getChild($filename))); exit;
        var_dump(($root->getChild($filename)->getContent())); exit;
        $this->assertTrue($root->hasChild($filename));
    }
}

class GenerateCommandStub extends GenerateCommand
{
    protected $version;
    protected $up;

    /**
     * @return mixed
     */
    public function getUp()
    {
        return "Resources/fixtures/SimpleUp";
    }
    protected $down;

    /**
     * @return mixed
     */
    public function getDown()
    {
        return "Resources/fixtures/SimpleDown";
    }

    public function getPrivateTemplate()
    {
        return self::$_template;
    }

    public function setVersionString($version)
    {
        $this->version = $version;
    }

    protected function getVersionString()
    {
        return $this->version;
    }


}
