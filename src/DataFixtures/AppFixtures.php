<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserRole;
use Couchbase\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    private UserPasswordEncoderInterface $encoder;
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setIsAdmin(true)
            ->setIsEnabled(true)
            ->setAdresse('Bamako')
            ->setEmail('yaranagoresekou@gmail.com')
            ->setNom('Baba')
            ->setPrenom('Yara')
            ->setCreatedAt(new \DateTimeImmutable('now'))
            ->setPassword($this->encoder->encodePassword($user, 'passer'))
            ->setTelephone('0022372000068');
        $role = new UserRole();
        $role->setLibelle('SuperAdmin')
            ->setDescription('Le rôle des super admin');
        $manager->persist($role);
        $user->addUserRole($role);
        $manager->persist($user);
        $manager->flush();
    }
}
