<?php
/**
 * User: aguidet
 * Date: 10/02/15
 * Time: 17:13
 */

namespace Deploy\Action;


use Deploy\Command\ComposerInstall;
use Deploy\Command\Mkdir;
use Deploy\Command\Rm;
use Deploy\Command\Scp;
use Deploy\Command\Symlink;
use Deploy\Command\TarGz;
use Deploy\Command\UnTarGz;

class Deploy extends AbstractAction {

    private $packageName;

    /**
     * Perform every task before processing the action
     */
    protected function begin()
    {
        // TODO: Implement begin() method.
    }

    /**
     * Tasks of the current action
     */
    protected function processing()
    {
        // pre-deploy
        // composer install etc ...
        $this->logger->addInfo("pre deploy");
        $composerCommand = new ComposerInstall(
            $this->config,
            $this->arguments,
            $this->logger
        );
        $composerCommand->runCommand();

        // create package
        $this->logger->addInfo("create package");
        $tarGzCommand = new TarGz(
            $this->config,
            $this->arguments,
            $this->logger
        );
        $tarGzCommand->runCommand();

        $this->logger->addInfo("on deploy");
        // deployment phase on each host
        foreach ($this->config->getHosts() as $host) {

            $this->config->setCurrentHost($host);

            // create TO directory
            $this->logger->addInfo("create distant directory");
            $mkdirCommand = new Mkdir(
                $this->config,
                $this->arguments,
                $this->logger
            );
            $mkdirCommand->runCommand();

            // upload package
            $this->logger->addInfo("upload package");
            $scpCommand = new Scp(
                $this->config,
                $this->arguments,
                $this->logger
            );
            $scpCommand->runCommand();

            $unTarGzCommand = new UnTarGz(
                $this->config,
                $this->arguments,
                $this->logger
            );
            $unTarGzCommand->runCommand();
        }
        // on-deploy
        // on each host after code has been copied
        $this->logger->addInfo("post deploy");

        $rmCommand = new Rm(
            $this->config,
            $this->arguments,
            $this->logger
        );
        $rmCommand->runCommand();

        foreach ($this->config->getHosts() as $host) {

            $this->config->setCurrentHost($host);

            $symlinkCommand = new Symlink(
                $this->config,
                $this->arguments,
                $this->logger
            );
            $symlinkCommand->runCommand();
        }

        // post-release
        // on each host after release has been activated
        $this->logger->addInfo("post release");

        // post-deploy
        // after deployment complete
        $this->logger->addInfo("after release");
    }

    /**
     * Perform every task after processing the action
     */
    protected function end()
    {
        // TODO: Implement end() method.
    }
}