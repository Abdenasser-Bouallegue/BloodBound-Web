<?php

namespace App\DataFixtures;

use App\Entity\ScratchCode;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ScratchCodeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $points = [2000, 4000, 6000];
        $letters = range('A', 'Z');
        
        for ($i = 0; $i < 101; $i++) {
            $scratchCode = new ScratchCode();
            $code = '';
            $codeParts = array_merge($letters, range(0, 9));
            shuffle($codeParts);
            for ($j = 0; $j < 4; $j++) {
                $code .= $codeParts[array_rand($codeParts)];
            }
            $code .= str_shuffle(str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT));
            $scratchCode->setCode($code);
            $scratchCode->setPoints($points[array_rand($points)]);
            $manager->persist($scratchCode);
        }
        
        $manager->flush();
    }
}
