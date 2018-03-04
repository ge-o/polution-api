<?php
/**
 * Created by PhpStorm.
 * User: geroduppel
 * Date: 03.03.18
 * Time: 14:07
 */

namespace App\Command;

use App\Entity\MeasureData;
use App\Entity\Station;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Repository\RepositoryFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DownloadCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    protected $em;

    protected $spreadsheet;

    protected $stations;

    const DATE_ROW = 'A';
    const TIME_ROW = 'B';

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:import')
            ->setDescription('Importer for polution data')
            ->setHelp('Imports polution data from the LANUV Website');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @todo: put this in a service
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = "https://www.lanuv.nrw.de/fileadmin/lanuv/luft/temes/NO2_AM1H.csv";
        $file = file_get_contents($filename);
        $target_filename = 'var/download/NO2_AM1H.csv';
        file_put_contents($target_filename, $file);


        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var RepositoryFactory $stationRepo */
        $stationRepo = $this->em->getRepository('App:Station');
        $measureRepo = $this->em->getRepository('App:MeasureData');
        $this->stations = $stationRepo->findAll();

        /** @var Csv $reader */
        $reader = new Csv();
        $reader->setDelimiter(";");
        $output->writeln('Import CSV File');
        /** @var Spreadsheet $spreadsheet */
        $this->spreadsheet = $reader->load($target_filename);
        $output->writeln('CSV File imported');
        $count_lines = $this->spreadsheet->getActiveSheet()->getHighestRow();

        for ($i = 3; $i <= $count_lines; $i++) {
            $date = $this->spreadsheet->getActiveSheet()->getCell(self::DATE_ROW.$i)->getValue();
            $time = $this->spreadsheet->getActiveSheet()->getCell(self::TIME_ROW.$i)->getValue();
            $measureDate = new \DateTime($date." ".$time);
            /** @var Station $station */
            foreach ($this->stations as $station) {
                $this->saveMeasureData($i, $measureDate, $station);
            }
        }
        $this->em->flush();
        $output->writeln('Data imported');
    }

    protected function saveMeasureData($line, $date, $station)
    {
        $m = new MeasureData();
        $m->setMeasureDate($date);
        $val = $this->spreadsheet->getActiveSheet()->getCell('AX'.$line)->getValue();
        if ($val) {
            $m->setNo2($val);
        }
        $m->setStation($station);
        $this->em->persist($m);
    }
}
