<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class CodeAnalysisService
{
    public function runPhpStanAnalysis(string $directory): Process
    {
        $process = new Process(['../../vendor/bin/phpstan', 'analyse']);
        $process->setWorkingDirectory($directory);
        $process->run();

        return $process;
    }

    public function runPhpCsAnalysis(string $directory): Process
    {
        $process = new Process(['../../vendor/bin/phpcs', '../../public/repoClone/src']);
        $process->setWorkingDirectory($directory);
        $process->run();

        return $process;
    }
}
