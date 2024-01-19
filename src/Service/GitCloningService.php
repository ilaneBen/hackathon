<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class GitCloningService
{
    public function cloneRepository(string $repositoryUrl, string $destination): Process
    {
        $process = new Process(['git', 'clone', $repositoryUrl, $destination]);
        $process->run();

        return $process;
    }
}
