<?php
/**
 * Created by PhpStorm.
 * User: geroduppel
 * Date: 03.03.18
 * Time: 14:07
 */

namespace App\Command;

use App\Entity\Station;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StationCommand
 * @package App\Command
 * @todo: remove the data from here to somewhere else
 */
class StationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:station')
            ->setDescription('Imports the 4 Cologne measure stations');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @todo: put this in a service
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $stationRepo = $em->getRepository('App:Station');


        $klevischer = $stationRepo->findOneByIdentifier('VKCL');
        if (!$klevischer) {
            $klevischer = new Station();
        }
        $turiner = $stationRepo->findOneByIdentifier('VKTU');
        if (!$turiner) {
            $turiner = new Station();
        }
        $chorweiler = $stationRepo->findOneByIdentifier('CHOR');
        if (!$chorweiler) {
            $chorweiler = new Station();
        }
        $rodenkirchen = $stationRepo->findOneByIdentifier('RODE');
        if (!$rodenkirchen) {
            $rodenkirchen = new Station();
        }

        $klevischer->setIdentifier('VKCL');
        $turiner->setIdentifier('VKTU');
        $chorweiler->setIdentifier('CHOR');
        $rodenkirchen->setIdentifier('RODE');

        $klevischer->setName('Köln Clevischer Ring');
        $turiner->setName('Köln Turiner Straße');
        $chorweiler->setName('Köln-Chorweiler');
        $rodenkirchen->setName('Köln-Rodenkirchen');

        $klevischer->setLatitude('50.964166667');
        $turiner->setLatitude('50.948611111');
        $chorweiler->setLatitude('51.020555556');
        $rodenkirchen->setLatitude('50.890833333');

        $klevischer->setLongitude('7.005277778');
        $turiner->setLongitude('6.958333333');
        $chorweiler->setLongitude('6.885277778');
        $rodenkirchen->setLongitude('6.985833333');

        $klevischer->setCsvRow('AX');
        $turiner->setCsvRow('AY');
        $chorweiler->setCsvRow('H');
        $rodenkirchen->setCsvRow('AF');

        $em->persist($klevischer);
        $em->persist($turiner);
        $em->persist($chorweiler);
        $em->persist($rodenkirchen);
        $em->flush();
    }
}
