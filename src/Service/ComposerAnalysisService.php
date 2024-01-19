<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class ComposerAnalysisService
{
    public function runComposerAudit(string $directory): Process
    {
        $process = new Process(['composer', 'audit', '--locked', '--format=json']);
        $process->setWorkingDirectory($directory);
        $process->run();

        return $process;
    }
}
