<?php

// src/Command/CreateUsersCommand.php

namespace App\Fixture;

use App\Entity\Job;
use App\Repository\ProjectRepository;
use App\Repository\RapportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobFixture extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProjectRepository $projectRepository,
        private RapportRepository $rapportRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:create-jobs')
            ->setDescription('Create jobs with Faker data for each type.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = Factory::create();
        $projects = $this->projectRepository->findAll();
        $rapports = $this->rapportRepository->findAll();

        $jobTypes = ['PHP Version', 'PHP Cs', 'PHP STAN', 'Composer Audit'];

        foreach ($rapports as $rapport) {
            foreach ($jobTypes as $type) {
                $job = new Job();
                $job->setName($type);
                $job->setProject($faker->randomElement($projects));

                // Ajoutez la logique spécifique pour chaque type de job
                switch ($type) {
                    case 'PHP Version':
                        $job->setResultat(1);
                        $job->setDetail(['result' => ['>=8.0.2']]);
                        break;

                    case 'PHP Cs':
                        $job->setResultat(1);
                        $job->setDetail([
                            'result' => [
                                [
                                    'twig/twig' => [
                                        [
                                            'cve' => 'CVE-2022-39261',
                                            'link' => 'https://symfony.com/blog/twig-security-release-possibility-to-load-a-template-outside-a-configured-directory-when-using-the-filesystem-loader',
                                            'title' => 'Possibility to load a template outside a configured directory when using the filesystem loader',
                                            'sources' => [
                                                ['name' => 'GitHub', 'remoteId' => 'GHSA-52m2-vc4m-jj33'],
                                                ['name' => 'FriendsOfPHP/security-advisories', 'remoteId' => 'twig/twig/2022-09-28.yaml'],
                                            ],
                                            'advisoryId' => 'PKSA-n7sg-8f52-pqtf',
                                            'reportedAt' => '2022-09-28T10:36:08+00:00',
                                            'packageName' => 'twig/twig',
                                            'affectedVersions' => '>=1.0.0,<1.44.7|>=2.0.0,<2.15.3|>=3.0.0,<3.4.3',
                                        ],
                                    ],
                                    'symfony/http-kernel' => [
                                        [
                                            'cve' => 'CVE-2022-24894',
                                            'link' => 'https://symfony.com/cve-2022-24894',
                                            'title' => 'CVE-2022-24894: Prevent storing cookie headers in HttpCache',
                                            'sources' => [
                                                ['name' => 'GitHub', 'remoteId' => 'GHSA-h7vf-5wrv-9fhv'],
                                                ['name' => 'FriendsOfPHP/security-advisories', 'remoteId' => 'symfony/http-kernel/CVE-2022-24894.yaml'],
                                            ],
                                            'advisoryId' => 'PKSA-hr4y-jwk2-1yb9',
                                            'reportedAt' => '2023-02-01T08:00:00+00:00',
                                            'packageName' => 'symfony/http-kernel',
                                            'affectedVersions' => '>=2.0.0,<2.1.0|>=2.1.0,<2.2.0|>=2.2.0,<2.3.0|>=2.3.0,<2.4.0|>=2.4.0,<2.5.0|>=2.5.0,<2.6.0|>=2.6.0,<2.7.0|>=2.7.0,<2.8.0|>=2.8.0,<3.0.0|>=3.0.0,<3.1.0|>=3.1.0,<3.2.0|>=3.2.0,<3.3.0|>=3.3.0,<3.4.0|>=3.4.0,<4.0.0|>=4.0.0,<4.1.0|>=4.1.0,<4.2.0|>=4.2.0,<4.3.0|>=4.3.0,<4.4.0|>=4.4.0,<4.4.50|>=5.0.0,<5.1.0|>=5.1.0,<5.2.0|>=5.2.0,<5.3.0|>=5.3.0,<5.4.0|>=5.4.0,<5.4.20|>=6.0.0,<6.0.20|>=6.1.0,<6.1.12|>=6.2.0,<6.2.6',
                                        ],
                                    ],
                                    'symfony/twig-bridge' => [
                                        [
                                            'cve' => 'CVE-2023-46734',
                                            'link' => 'https://symfony.com/cve-2023-46734',
                                            'title' => 'CVE-2023-46734: Potential XSS vulnerabilities in CodeExtension filters',
                                            'sources' => [
                                                ['name' => 'GitHub', 'remoteId' => 'GHSA-q847-2q57-wmr3'],
                                                ['name' => 'FriendsOfPHP/security-advisories', 'remoteId' => 'symfony/twig-bridge/CVE-2023-46734.yaml'],
                                            ],
                                            'advisoryId' => 'PKSA-ztgh-x9c8-k66g',
                                            'reportedAt' => '2023-11-10T08:00:00+00:00',
                                            'packageName' => 'symfony/twig-bridge',
                                            'affectedVersions' => '>=2.0.0,<2.1.0|>=2.1.0,<2.2.0|>=2.2.0,<2.3.0|>=2.3.0,<2.4.0|>=2.4.0,<2.5.0|>=2.5.0,<2.6.0|>=2.6.0,<2.7.0|>=2.7.0,<2.8.0|>=2.8.0,<3.0.0|>=3.0.0,<3.1.0|>=3.1.0,<3.2.0|>=3.2.0,<3.3.0|>=3.3.0,<3.4.0|>=3.4.0,<4.0.0|>=4.0.0,<4.1.0|>=4.1.0,<4.2.0|>=4.2.0,<4.3.0|>=4.3.0,<4.4.0|>=4.4.0,<4.4.51|>=5.0.0,<5.1.0|>=5.1.0,<5.2.0|>=5.2.0,<5.3.0|>=5.3.0,<5.4.0|>=5.4.0,<5.4.31|>=6.0.0,<6.1.0|>=6.1.0,<6.2.0|>=6.2.0,<6.3.0|>=6.3.0,<6.3.8',
                                        ],
                                    ],
                                    'symfony/security-http' => [
                                        [
                                            'cve' => 'CVE-2023-46733',
                                            'link' => 'https://symfony.com/cve-2023-46733',
                                            'title' => 'CVE-2023-46733: Possible session fixation',
                                            'sources' => [
                                                ['name' => 'GitHub', 'remoteId' => 'GHSA-m2wj-r6g3-fxfx'],
                                                ['name' => 'FriendsOfPHP/security-advisories', 'remoteId' => 'symfony/security-http/CVE-2023-46733.yaml'],
                                            ],
                                            'advisoryId' => 'PKSA-5x8m-77gx-t86z',
                                            'reportedAt' => '2023-11-10T08:00:00+00:00',
                                            'packageName' => 'symfony/security-http',
                                            'affectedVersions' => '>=5.4.0,<5.4.31|>=6.0.0,<6.1.0|>=6.1.0,<6.2.0|>=6.2.0,<6.3.0|>=6.3.0,<6.3.8',
                                        ],
                                    ],
                                    'symfony/security-bundle' => [
                                        [
                                            'cve' => 'CVE-2022-24895',
                                            'link' => 'https://symfony.com/cve-2022-24895',
                                            'title' => 'CVE-2022-24895: Possible CSRF token fixation',
                                            'sources' => [
                                                ['name' => 'GitHub', 'remoteId' => 'GHSA-3gv2-29qc-v67m'],
                                                ['name' => 'FriendsOfPHP/security-advisories', 'remoteId' => 'symfony/security-bundle/CVE-2022-24895.yaml'],
                                            ],
                                            'advisoryId' => 'PKSA-4w9w-fp7s-ddy7',
                                            'reportedAt' => '2023-02-01T08:00:00+00:00',
                                            'packageName' => 'symfony/security-bundle',
                                            'affectedVersions' => '>=2.0.0,<2.1.0|>=2.1.0,<2.2.0|>=2.2.0,<2.3.0|>=2.3.0,<2.4.0|>=2.4.0,<2.5.0|>=2.5.0,<2.6.0|>=2.6.0,<2.7.0|>=2.7.0,<2.8.0|>=2.8.0,<3.0.0|>=3.0.0,<3.1.0|>=3.1.0,<3.2.0|>=3.2.0,<3.3.0|>=3.3.0,<3.4.0|>=3.4.0,<4.0.0|>=4.0.0,<4.1.0|>=4.1.0,<4.2.0|>=4.2.0,<4.3.0|>=4.3.0,<4.4.0|>=4.4.0,<4.4.50|>=5.0.0,<5.1.0|>=5.1.0,<5.2.0|>=5.2.0,<5.3.0|>=5.3.0,<5.4.0|>=5.4.0,<5.4.20|>=6.0.0,<6.0.20|>=6.1.0,<6.1.12|>=6.2.0,<6.2.6',
                                        ],
                                    ],
                                ],
                            ],
                        ]);

                        break;

                    case 'PHP STAN':
                        $job->setResultat(1);
                        $job->setDetail(['result' => []]);
                        break;

                    case 'Composer Audit':
                        $job->setResultat(1);
                        $job->setDetail([
                            'result' => [
                                [
                                    'errors' => 11,
                                    'fixable' => 8,
                                    'warnings' => 21,
                                ],
                                [
                                    '/Users/alb-cr/web/hackathon/public/repoClone/src/Controller/CategoriesController.php' => [
                                        'errors' => 3,
                                        'messages' => [
                                            [
                                                'line' => 2,
                                                'type' => 'ERROR',
                                                'column' => 1,
                                                'source' => 'PEAR.Commenting.FileComment.Missing',
                                                'fixable' => false,
                                                'message' => 'Missing file doc comment',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 13,
                                                'type' => 'ERROR',
                                                'column' => 1,
                                                'source' => 'PEAR.Commenting.ClassComment.Missing',
                                                'fixable' => false,
                                                'message' => 'Missing doc comment for class CategoriesController',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 16,
                                                'type' => 'ERROR',
                                                'column' => 12,
                                                'source' => 'PEAR.Commenting.FunctionComment.Missing',
                                                'fixable' => false,
                                                'message' => 'Missing doc comment for function list()',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 16,
                                                'type' => 'WARNING',
                                                'column' => 107,
                                                'source' => 'Generic.Files.LineLength.TooLong',
                                                'fixable' => false,
                                                'message' => 'Line exceeds 85 characters; contains 114 characters',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 22,
                                                'type' => 'WARNING',
                                                'column' => 95,
                                                'source' => 'Generic.Files.LineLength.TooLong',
                                                'fixable' => false,
                                                'message' => 'Line exceeds 85 characters; contains 95 characters',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 24,
                                                'type' => 'WARNING',
                                                'column' => 91,
                                                'source' => 'Generic.Files.LineLength.TooLong',
                                                'fixable' => false,
                                                'message' => 'Line exceeds 85 characters; contains 91 characters',
                                                'severity' => 5,
                                            ],
                                        ],
                                        'warnings' => 3,
                                    ],
                                ],
                                [
                                    '/Users/alb-cr/web/hackathon/public/repoClone/src/DataFixtures/CategoriesFixtures.php' => [
                                        'errors' => 8,
                                        'messages' => [
                                            [
                                                'line' => 2,
                                                'type' => 'ERROR',
                                                'column' => 1,
                                                'source' => 'PEAR.Commenting.FileComment.Missing',
                                                'fixable' => false,
                                                'message' => 'Missing file doc comment',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 10,
                                                'type' => 'ERROR',
                                                'column' => 1,
                                                'source' => 'PEAR.Commenting.ClassComment.Missing',
                                                'fixable' => false,
                                                'message' => 'Missing doc comment for class CategoriesFixtures',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 12,
                                                'type' => 'ERROR',
                                                'column' => 13,
                                                'source' => 'PEAR.NamingConventions.ValidVariableName.PrivateNoUnderscore',
                                                'fixable' => false,
                                                'message' => 'Private member variable "counter" must be prefixed with an underscore',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 14,
                                                'type' => 'ERROR',
                                                'column' => 12,
                                                'source' => 'PEAR.Commenting.FunctionComment.Missing',
                                                'fixable' => false,
                                                'message' => 'Missing doc comment for function __construct()',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 14,
                                                'type' => 'ERROR',
                                                'column' => 67,
                                                'source' => 'PEAR.Functions.FunctionDeclaration.BraceOnSameLine',
                                                'fixable' => true,
                                                'message' => 'Opening brace should be on a new line',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 14,
                                                'type' => 'ERROR',
                                                'column' => 68,
                                                'source' => 'PEAR.WhiteSpace.ScopeClosingBrace.Line',
                                                'fixable' => true,
                                                'message' => 'Closing brace must be on a line by itself',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 16,
                                                'type' => 'ERROR',
                                                'column' => 12,
                                                'source' => 'PEAR.Commenting.FunctionComment.Missing',
                                                'fixable' => false,
                                                'message' => 'Missing doc comment for function load()',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 33,
                                                'type' => 'ERROR',
                                                'column' => 12,
                                                'source' => 'PEAR.Commenting.FunctionComment.Missing',
                                                'fixable' => false,
                                                'message' => 'Missing doc comment for function createCategory()',
                                                'severity' => 5,
                                            ],
                                            [
                                                'line' => 33,
                                                'type' => 'WARNING',
                                                'column' => 99,
                                                'source' => 'Generic.Files.LineLength.TooLong',
                                                'fixable' => false,
                                                'message' => 'Line exceeds 85 characters; contains 99 characters',
                                                'severity' => 5,
                                            ],
                                        ],
                                        'warnings' => 1,
                                    ],
                                ],
                            ],
                        ]);

                        break;
                }

                $job->setRapport($rapport);
                $job->setDate($rapport->getDate()); // Utilise la date du rapport

                $this->entityManager->persist($job);
            }
        }

        $this->entityManager->flush();

        $output->writeln('Jobs created successfully.');

        return Command::SUCCESS;
    }
}
