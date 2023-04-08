<?php

namespace App\Entity;

use App\Repository\PickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PickRepository::class)]
class Pick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'picks')]
    private ?User $player = null;

    #[ORM\ManyToOne(inversedBy: 'picks')]
    private ?Champion $champion = null;

    #[ORM\OneToMany(mappedBy: 'pick', targetEntity: Matchup::class)]
    private Collection $matchups;

    public function __construct()
    {
        $this->matchups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?User
    {
        return $this->player;
    }

    public function setPlayer(?User $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getChampion(): ?Champion
    {
        return $this->champion;
    }

    public function setChampion(?Champion $champion): self
    {
        $this->champion = $champion;

        return $this;
    }

    /**
     * @return Collection<int, Matchup>
     */
    public function getMatchups(): Collection
    {
        return $this->matchups;
    }

    public function addMatchup(Matchup $matchup): self
    {
        if (!$this->matchups->contains($matchup)) {
            $this->matchups->add($matchup);
            $matchup->setPick($this);
        }

        return $this;
    }

    public function removeMatchup(Matchup $matchup): self
    {
        if ($this->matchups->removeElement($matchup)) {
            // set the owning side to null (unless already changed)
            if ($matchup->getPick() === $this) {
                $matchup->setPick(null);
            }
        }

        return $this;
    }
    public function getWinRate(): string
    {
        $matchups = $this->getMatchups();
        $totalGames = $wonGames = 0;
        foreach ($matchups as $matchup) {
            $totalGames = $totalGames + $matchup->getTotalGames();
            $wonGames = $wonGames + $matchup->getWonGames();
        }
        return (($wonGames / $totalGames) * 100);
    }
    public function getWinLanesRate(): string
    {
        $matchups = $this->getMatchups();
        $totalGames = $wonGames = 0;
        foreach ($matchups as $matchup) {
            $totalGames = $totalGames + $matchup->getTotalLanes();
            $wonGames = $wonGames + $matchup->getWonLanes();
        }
        return (($wonGames / $totalGames) * 100);
    }
}
