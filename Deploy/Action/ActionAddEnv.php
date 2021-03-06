<?php
/**
 * User: aguidet
 * Date: 13/02/15
 * Time: 16:39
 */

namespace Deploy\Action;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActionAddEnv extends Command
{
    protected function configure()
    {
        $this
            ->setName('config:addenv')
            ->setDescription('Create the default environment configuration file, a .php-deploy/environments/{env}.ini default file will be created')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'The environment name to create',
                'dev'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $configurationPath = getcwd() . '/.php-deploy';
        $envPath = $configurationPath . '/environments';
        $env = $input->getArgument('name');

        if (!is_dir($envPath)) {
            $output->writeln("<error>Project has not been initialized, please initialize it !!!</error>");
        } else if (!file_exists($envPath . '/' . $env . '.ini')) {

            $output->writeln("<info>Creating default $env.ini file</info>");

            exec(
                sprintf(
                    "cp %s %s",
                    __DIR__ . '/../../templates/env.yml',
                    $envPath . "/$env.yml"
                )
            );


            $output->writeln("<info>Environment $env added</info>");
            $output->writeln("<info>Please edit .php-deploy/environments/$env.yml</info>");

        } else {

            $output->writeln("<error>Environments already exists !!!</error>");

        }
    }
}