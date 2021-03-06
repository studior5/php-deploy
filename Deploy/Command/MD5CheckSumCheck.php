<?php
/**
 * User: aguidet
 * Date: 09/03/15
 * Time: 11:13
 */

namespace Deploy\Command;


use Deploy\Util\NameUtil;

class MD5CheckSumCheck extends AbstractCommand
{
    /**
     * execute command and php tasks
     * return the execution status as an integer
     *
     * @return int
     */
    public function run()
    {
        if ($this->get('destination') == null) {
            $class = get_class($this);
            throw new \InvalidArgumentException("destination argument is mandatory for $class command, please check you configuration");
        }

        $directoryName = NameUtil::generateDirectoryName(
            $this->getProjectName(),
            $this->input
        );

        $command = sprintf(
            'ssh %s@%s "cd %s ; find . ! -name CHECKSUM.md5 -exec md5sum {} + | sort | md5sum"',
            exec('whoami'),
            $this->getCurrentHost(),
            $this->get('destination') . '/' . $directoryName
        );

        $this->shellExec($command);

        $found = $this->commandOutput;

        $command = sprintf(
            'ssh %s "cd %s ; cat CHECKSUM.md5"',
            $this->getCurrentHost(),
            $this->get('destination') . '/' . $directoryName
        );

        $this->shellExec($command);

        $expected = $this->commandOutput;

        if (!$this->isDry()) {

            $found = trim($found);
            $expected = trim($expected);

            if ($expected != $found) {
                throw new \RuntimeException("extracted code MD5 sum differs from package expected [$expected] found [$found]");
            }
        }
    }
}