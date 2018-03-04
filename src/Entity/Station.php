<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

/**
 * @ApiResource(collectionOperations={"get"},
 *     itemOperations={"get"},
 *     attributes={
 *     "filters"={
 *             "station.date_filter"
 *         },
 *     "normalization_context"={"groups"={"read"}}})
 * @ApiFilter(SearchFilter::class, properties={"name": "partial","measureData.meshareDate": "exact"})
 * @ApiFilter(DateFilter::class, properties={"measureData.measureDate"})
 * @ORM\Entity()
 */
class Station
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups("read")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     * @Groups("read")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", nullable=true)
     * @Groups("read")
     */
    protected $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string", nullable=true)
     * @Groups("read")
     */
    protected $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string", nullable=true)
     * @Groups("read")
     */
    protected $latitude;

    /**
     * @var MeasureData $measureData
     *
     * @ORM\OneToMany(targetEntity="App\Entity\MeasureData", mappedBy="station")
     * @ORM\OrderBy({"measureDate" = "DESC"})
     * @Groups("read")
     */
    protected $measureData;

    /**
     * @var string
     *
     * @ORM\Column(name="csv_row", type="string", nullable=true)
     */
    protected $csvRow;

    public function __construct()
    {
        $this->measureData = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return MeasureData
     */
    public function getMeasureData()
    {
        return $this->measureData;
    }

    /**
     * @param MeasureData $measuredData
     */
    public function setMeasureData(MeasureData $measuredData): void
    {
        $this->measuredData = $measuredData;
    }

    public function addMeasureData(MeasureData $measuredData)
    {
        if (! $this->measureData->contains($measuredData)) {
            $this->measureData->add($measuredData);
        }
    }

    public function removeMeasureData(MeasureData $measuredData)
    {
        if ($this->measureData->contains($measuredData)) {
            $this->measureData->removeElement($measuredData);
        }
    }

    /**
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude(string $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude(string $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getCsvRow(): string
    {
        return $this->csvRow;
    }

    /**
     * @param string $csvRow
     */
    public function setCsvRow(string $csvRow): void
    {
        $this->csvRow = $csvRow;
    }
}
