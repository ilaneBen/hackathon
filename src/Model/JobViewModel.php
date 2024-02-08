<?php

namespace App\Model;

use App\Entity\Job;

class JobViewModel
{
    public function __construct(private Job $job)
    {
    }

    public function getId(): int
    {
        return $this->job->getId();
    }

    public function getName(): string
    {
        return $this->job->getName();
    }

    public function getResultat(): bool
    {
        return $this->job->isResultat();
    }

    public function getProjectName(): string
    {
        return $this->job->getProject()->getName();
    }

    private function getTotalVulnerabilities($composerAuditResults)
    {
        $totalVulnerabilities = 0;

        foreach ($composerAuditResults as $result) {
            $totalVulnerabilities += count($result['Vulnerabilities']);
        }

        return $totalVulnerabilities;
    }

    public function getDetails(): array
    {
        $details = $this->job->getDetail();

        switch ($this->job->getName()) {
            case 'Composer Audit':
                return $this->getComposerAuditDetails($details);
            case 'PHP STAN':
                return $this->getPHPStanDetails($details);
            case 'PHP Cs':
                return $this->getPHPCsDetails($details);
            case 'Style Lint':
                return $this->getStyleLintDetails($details);
            case 'Eslint':
                return $this->getEslintDetails($details);
            case 'Yarn audit':
                return $this->getYarnAuditDetails($details);
            case 'NPM audit':
                return $this->getNpmAuditDetails($details);
            case 'PHP Version':
                return [
                    'Version minimale requise' => $details['result'][0] ?? 'Non spécifié',
                ];
            default:
                return $details;
        }
    }

    private function getPHPStanDetails(array $details): array
    {
        $phpStanResults = [];
        $phpStanResults['Erreurs'] = 0;
        foreach ($details['result'] as $index => $result) {
            $files = [];

            if (isset($result['file_errors'])) {
                $phpStanResults['Erreurs'] = $result['file_errors'];
            } else {
                foreach ($result as $filePath => $fileDetails) {
                    if ('errors' !== $filePath && 'warnings' !== $filePath && 'fixable' !== $filePath) {
                        $files[] = [
                            'Chemin du fichier' => $filePath,
                            'Erreurs' => $fileDetails['errors'] ?? 0,
                            'Avertissements' => $fileDetails['warnings'] ?? 0,
                            'Messages' => $fileDetails['messages'] ?? [],
                        ];
                    }
                }
            }

            $phpStanResults[] = [
                'Index' => $index + 1,
                'Erreurs' => $result['errors'] ?? 0,
                'Avertissements' => $result['warnings'] ?? 0,
                'Fichiers' => $files,
                // Ajoutez d'autres éléments si nécessaire
            ];
        }

        return [
            'Résultats de PHP STAN' => $phpStanResults,
        ];
    }

    private function getPHPCsDetails(array $details): array
    {
        $phpCsResults = [];

        foreach ($details['result'] as $index => $result) {
            $files = [];

            foreach ($result as $filePath => $fileDetails) {
                if ('errors' !== $filePath && 'warnings' !== $filePath && 'fixable' !== $filePath) {
                    $files[] = [
                        'Chemin du fichier' => $filePath,
                        'Erreurs' => $fileDetails['errors'] ?? 0,
                        'Avertissements' => $fileDetails['warnings'] ?? 0,
                        'Messages' => $fileDetails['messages'] ?? [],
                    ];
                }
            }

            $phpCsResults[] = [
                'Index' => $index + 1,
                'Erreurs' => $result['errors'] ?? 0,
                'Avertissements' => $result['warnings'] ?? 0,
                'Réparable' => $result['fixable'] ?? 0,
                'Fichiers' => $files,
                // Ajoutez d'autres éléments si nécessaire
            ];
        }

        return [
            'Résultats de PHP Cs' => $phpCsResults,
        ];
    }

    private function getStyleLintDetails(array $details): array
    {
        $styleLineResults = [];

        $totalJobWarnings = 0;
        $totalJobParseErrors = 0;
        $totalJobDeprecations = 0;
        $totalJobInvalidOptionWarnings = 0;

        foreach ($details['result'] as $item) {
            $file = $item['source'];
            $errored = $item['errored'];
            $warnings = $item['warnings'];
            $parseErrors = $item['parseErrors'];
            $deprecations = $item['deprecations'];
            $invalidOptionWarnings = $item['invalidOptionWarnings'];

            // Total warnings for each file
            $totalWarnings = count($warnings);
            $totalParseErrors = count($parseErrors);
            $totalDeprecations = count($deprecations);
            $totalInvalidOptionWarnings = count($invalidOptionWarnings);

            // Add totals to the global job totals
            $totalJobWarnings += $totalWarnings;
            $totalJobParseErrors += $totalParseErrors;
            $totalJobDeprecations += $totalDeprecations;
            $totalJobInvalidOptionWarnings += $totalInvalidOptionWarnings;

            $styleLineResults[] = [
                'file' => $file,
                'errored' => $errored,
                'warnings' => $warnings,
                'parseErrors' => $parseErrors,
                'deprecations' => $deprecations,
                'invalidOptionWarnings' => $invalidOptionWarnings,
                'totalWarnings' => $totalWarnings,
                'totalParseErrors' => $totalParseErrors,
                'totalDeprecations' => $totalDeprecations,
                'totalInvalidOptionWarnings' => $totalInvalidOptionWarnings,
            ];
        }

        return [
            'Style Line Results' => $styleLineResults,
            'Total Job Warnings' => $totalJobWarnings,
            'Total Job Parse Errors' => $totalJobParseErrors,
            'Total Job Deprecations' => $totalJobDeprecations,
            'Total Job Invalid Option Warnings' => $totalJobInvalidOptionWarnings,
        ];
    }

    private function getEslintDetails(array $details): array
    {
        $eslintResults = [];
        $totalErrors = 0;
        $totalWarnings = 0;
        $totalFixable = 0;

        foreach ($details['result'] as $result) {
            $eslintResults[] = [
                'filePath' => $result['filePath'],
                'messages' => $result['messages'],
                'errorCount' => $result['errorCount'],
                'warningCount' => $result['warningCount'],
                'fatalErrorCount' => $result['fatalErrorCount'],
                'fixableErrorCount' => $result['fixableErrorCount'],
                'fixableWarningCount' => $result['fixableWarningCount'],
                'obseleteRules' => $result['usedDeprecatedRules'],
            ];

            $totalErrors += $result['errorCount'] + $result['fatalErrorCount'];
            $totalWarnings += $result['warningCount'];
            $totalFixable += $result['fixableErrorCount'] + $result['fixableWarningCount'];
        }

        return [
            'eslintResults' => $eslintResults,
            'totalErrors' => $totalErrors,
            'totalWarnings' => $totalWarnings,
            'totalFixable' => $totalFixable,
        ];
    }

    private function getComposerAuditDetails(array $details)
    {
        $composerAuditResults = [];
        foreach ($details['result'] as $packageResult) {
            foreach ($packageResult as $packageName => $vulnerabilities) {
                $vulnerabilityDetails = [];

                foreach ($vulnerabilities as $vulnerability) {
                    $vulnerabilityDetails[] = [
                        'CVE' => $vulnerability['cve'] ?? 'Non spécifié',
                        'Link' => $vulnerability['link'] ?? 'Non spécifié',
                        'Title' => $vulnerability['title'] ?? 'Non spécifié',
                        'Sources' => $vulnerability['sources'] ?? [],
                        'Advisory ID' => $vulnerability['advisoryId'] ?? 'Non spécifié',
                        'Reported At' => $vulnerability['reportedAt'] ?? 'Non spécifié',
                    ];
                }
                $totalVulnerabilities = count($vulnerabilityDetails);
                $composerAuditResults[] = [
                    'Package Name' => $packageName,
                    'Vulnerabilities' => $vulnerabilityDetails,
                    'Total Vulnerabilities' => $totalVulnerabilities,
                ];
            }
        }

        return [
            'Composer Audit Results' => $composerAuditResults,
            'Total Vulnerabilities' => $this->getTotalVulnerabilities($composerAuditResults),
        ];
    }

    public function getYarnAuditDetails(array $yarnAuditDetails): array
    {
        $yarnAuditResults = [];

        foreach ($yarnAuditDetails['result'] as $auditResult) {
            if ('auditAdvisory' === $auditResult['type']) {
                $advisory = $auditResult['data']['advisory'];

                $yarnAuditResults[] = [
                    'Package Name' => $advisory['module_name'],
                    'Vulnerabilities' => [
                        [
                            'CVE' => $advisory['cves'][0] ?? 'Non spécifié',
                            'Link' => $advisory['url'] ?? 'Non spécifié',
                            'Title' => $advisory['title'] ?? 'Non spécifié',
                            'Severity' => $advisory['severity'] ?? 'Non spécifié',
                            // Ajoutez d'autres éléments si nécessaire
                        ],
                    ],
                    'Total Vulnerabilities' => 1,
                ];
            }
        }

        return [
            'Yarn Audit Results' => $yarnAuditResults,
            'Total Vulnerabilities' => $this->getTotalVulnerabilities($yarnAuditResults),
        ];
    }

    private function getNpmAuditDetails(array $npmAuditDetails): array
    {
        $npmAuditResults = [];

        foreach ($npmAuditDetails['result'] as $index => $result) {
            if (0 === $index) {
                // Skip the first result (assuming it's an integer)
                continue;
            }

            $dependencies = $result['dependencies'] ?? [];
            $vulnerabilities = $result['vulnerabilities'] ?? [];

            $npmAuditResults[] = [
                'Dependencies' => $dependencies,
                'Vulnerabilities' => $vulnerabilities,
            ];
        }

        return [
            'Npm Audit Results' => $npmAuditResults,
        ];
    }
}
