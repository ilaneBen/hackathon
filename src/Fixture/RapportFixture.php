<?php

namespace App\Fixture;

use App\Entity\Rapport;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RapportFixture extends Command
{
    public function __construct(private EntityManagerInterface $entityManager, private ProjectRepository $projectRepository)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:create-rapports')
            ->setDescription('Create 400 rapports with Faker data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = Factory::create();

        $projects = $this->projectRepository->findAll();

        for ($i = 0; $i < 900; ++$i) {
            $dateWithinOneYear = new \DateTimeImmutable();
            $dateWithinOneYear = $dateWithinOneYear->sub(new \DateInterval('P1Y'));

            $randomDays = rand(0, 364);
            $dateWithinOneYear = $dateWithinOneYear->add(new \DateInterval("P{$randomDays}D"));
            $rapport = new Rapport();
            $rapport->setDate($dateWithinOneYear);
            $rapport->setContent('Rapport');
            $rapport->setProject($faker->randomElement($projects));

            $this->entityManager->persist($rapport);
        }

        $this->entityManager->flush();

        $output->writeln('400 rapports created successfully.');

        return Command::SUCCESS;
    }
}
