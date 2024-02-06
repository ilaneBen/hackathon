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

        // URLs for the three different repositories
        $urls = [
            'https://github.com/rahdyyAlb/e-commerce_alb.git',
            'https://github.com/rahdyyAlb/Mon-blog-info.git',
            'https://github.com/ilaneBen/hackathon.git',
        ];

        // Retrieve all users from the database
        $users = $this->entityManager->getRepository(User::class)->findAll();

        // Create 600 projects for each URL
        for ($i = 0; $i < 1200; ++$i) {
            $project = new Project();
            $project->setName($faker->word); // You can customize the project name generation
            $project->setUrl($urls[$i % 3]); // Cycle through the URLs
            $project->setDate(new \DateTime()); // Set the current date

            // Assign a user
            $user = $users[array_rand($users)]; // Pick a random user from the array
            $project->setUser($user);

            $this->entityManager->persist($project);
        }

        $this->entityManager->flush();

        $output->writeln('600 projects created successfully.');

        return Command::SUCCESS;
    }
}
