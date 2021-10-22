<?php

namespace App\DataFixtures;

use App\Module\Cron\Factory\CronJobFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CronJobFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jobCC = CronJobFactory::cacheClear();
        $jobFoo = CronJobFactory::dummyFoo();
        $jobBar = CronJobFactory::dummyBar();

        $manager->persist($jobCC);
        $manager->persist($jobFoo);
        $manager->persist($jobBar);

        $manager->flush();
    }
}
