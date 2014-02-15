<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Composer\Console\Application as ComposerApplication;
use Composer\IO\ConsoleIO;
use Composer\Factory;

class RequireCommand extends \Composer\Command\RequireCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription("Adds plugin packages to your .altax/composer.json under the current directory.")
            ->addOption(
                '--working-dir',
                '-d',
                InputOption::VALUE_REQUIRED,
                'If specified, use the given directory as working directory.'
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($newWorkDir = $this->getNewWorkingDir($input)) {
            $oldWorkingDir = getcwd();
            chdir($newWorkDir);
        }

        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        $composer = Factory::create($io);
        $this->setComposer($composer);
        $this->setIO($io);

        $statusCode = parent::execute($input, $output);
        
        if (isset($oldWorkingDir)) {
            chdir($oldWorkingDir);
        }

        return $statusCode;
    }

    /**
     * @param  InputInterface    $input
     * @throws \RuntimeException
     */
    private function getNewWorkingDir(InputInterface $input)
    {
        $workingDir = $input->getParameterOption(array('--working-dir', '-d'));
        if (false !== $workingDir && !is_dir($workingDir)) {
            throw new \RuntimeException('Invalid working directory specified.');
        }

        if (false === $workingDir) {
            $workingDir = getcwd()."/.altax";
        }

        return $workingDir;
    }

}