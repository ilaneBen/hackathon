<?php

// src/Command/CreateUsersCommand.php

namespace App\Fixture;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectFixture extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:create-projects')
            ->setDescription('Create 600 projects with Faker data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = Factory::create();

        $urls = [
            'https://github.com/rahdyyAlb/e-commerce_alb.git',
            'https://github.com/rahdyyAlb/Mon-blog-info.git',
            'https://github.com/ilaneBen/hackathon.git',
        ];

        $users = $this->entityManager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 1200; ++$i) {
            $dateWithinOneYear = new \DateTimeImmutable();
            $dateWithinOneYear = $dateWithinOneYear->sub(new \DateInterval('P1Y'));

            $randomDays = rand(0, 364);
            $dateWithinOneYear = $dateWithinOneYear->add(new \DateInterval("P{$randomDays}D"));
            $project = new Project();
            $project->setName($faker->word);
            $project->setUrl($urls[$i % 3]);
            $project->setDate($dateWithinOneYear);

            $user = $users[array_rand($users)];
            $project->setUser($user);

            $this->entityManager->persist($project);
        }

        $this->entityManager->flush();

        $output->writeln('600 projects created successfully.');

        return Command::SUCCESS;
    }
}
