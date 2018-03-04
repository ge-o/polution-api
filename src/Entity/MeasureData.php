<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 */
class MeasureData
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Station $station
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Station",inversedBy="measureData")
     */
    protected $station;

    /**
     * @var string
     *
     * @ORM\Column(name="no2", type="string", nullable=true)
     * @Groups("read")
     */
    protected $no2;

    /**
     * @var \DateTime
     * @ORM\Column(name="measure_date", type="datetime", nullable=false)
     * @Groups("read")
     */
    protected $measureDate;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Station
     */
    public function getStation(): Station
    {
        return $this->station;
    }

    /**
     * @param Station $station
     */
    public function setStation(Station $station): void
    {
        $this->station = $station;
    }

    /**
     * @return string
     */
    public function getNo2()
    {
        return $this->no2;
    }

    /**
     * @param string $no2
     */
    public function setNo2(string $no2): void
    {
        $this->no2 = $no2;
    }

    /**
     * @return \DateTime
     */
    public function getMeasureDate(): \DateTime
    {
        return $this->measureDate;
    }

    /**
     * @param \DateTime $measureDate
     */
    public function setMeasureDate(\DateTime $measureDate): void
    {
        $this->measureDate = $measureDate;
    }
}
