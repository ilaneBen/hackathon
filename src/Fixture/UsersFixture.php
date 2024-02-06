<?php

// src/Command/CreateUsersCommand.php

namespace App\Fixture;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UsersFixture extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setName('app:create-users')
            ->setDescription('Create 400 users with Faker data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = Factory::create();
        $generatedEmails = [];

        // Create an admin user
        $adminUser = new User();
        $adminEmail = $this->getUniqueEmail($faker->email, $generatedEmails);
        $adminUser->setEmail($adminEmail);
        $adminUser->setPassword(password_hash('adminpassword', PASSWORD_BCRYPT));
        $adminUser->setName($faker->lastName);
        $adminUser->setFirstName($faker->firstName);
        $adminUser->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($adminUser);
        $generatedEmails[] = $adminEmail;

        // Create 399 regular users
        for ($i = 0; $i < 499; ++$i) {
            $user = new User();
            $userEmail = $this->getUniqueEmail($faker->email, $generatedEmails);
            $user->setEmail($userEmail);
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setName($faker->lastName);
            $user->setFirstName($faker->firstName);
            $user->setRoles(['ROLE_USER']);

            $this->entityManager->persist($user);
            $generatedEmails[] = $userEmail;
        }

        $this->entityManager->flush();

        $output->writeln('401 users created successfully (1 admin, 400 users).');

        return Command::SUCCESS;
    }

    private function getUniqueEmail(string $email, array $usedEmails): string
    {
        while (in_array($email, $usedEmails)) {
            $email = Factory::create()->email;
        }

        return $email;
    }
}
