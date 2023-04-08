<?php

namespace App\Entity;

use App\Repository\MatchupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchupRepository::class)]
class Matchup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'matchups')]
    private ?Pick $pick = null;

    #[ORM\ManyToOne(inversedBy: 'matchups')]
    private ?Champion $opponent = null;

    #[ORM\Column(nullable: true)]
    private ?int $wonGames = null;

    #[ORM\Column(nullable: true)]
    private ?int $wonLanes = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalGames = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalLanes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPick(): ?Pick
    {
        return $this->pick;
    }

    public function setPick(?Pick $pick): self
    {
        $this->pick = $pick;

        return $this;
    }

    public function getOpponent(): ?Champion
    {
        return $this->opponent;
    }

    public function setOpponent(?Champion $opponent): self
    {
        $this->opponent = $opponent;

        return $this;
    }

    public function getWonGames(): ?int
    {
        return $this->wonGames;
    }

    public function setWonGames(?int $wonGames): self
    {
        $this->wonGames = $wonGames;

        return $this;
    }

    public function getWonLanes(): ?int
    {
        return $this->wonLanes;
    }

    public function setWonLanes(?int $wonLanes): self
    {
        $this->wonLanes = $wonLanes;

        return $this;
    }

    public function getTotalGames(): ?int
    {
        return $this->totalGames;
    }

    public function setTotalGames(?int $totalGames): self
    {
        $this->totalGames = $totalGames;

        return $this;
    }

    public function getTotalLanes(): ?int
    {
        return $this->totalLanes;
    }

    public function setTotalLanes(?int $totalLanes): self
    {
        $this->totalLanes = $totalLanes;

        return $this;
    }
}
