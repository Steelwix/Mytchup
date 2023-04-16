<?php

namespace App\Entity;

use App\Repository\ChampionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ChampionRepository::class)]
class Champion
{
    /**
     * @Groups({"getChampion", "getEncounter"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @Groups({"getChampion", "getEncounter"})
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'champion', targetEntity: Pick::class)]
    private Collection $picks;

    #[ORM\OneToMany(mappedBy: 'opponent', targetEntity: Matchup::class)]
    private Collection $matchups;

    public function __construct()
    {
        $this->picks = new ArrayCollection();
        $this->matchups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Pick>
     */
    public function getPicks(): Collection
    {
        return $this->picks;
    }

    public function addPick(Pick $pick): self
    {
        if (!$this->picks->contains($pick)) {
            $this->picks->add($pick);
            $pick->setChampion($this);
        }

        return $this;
    }

    public function removePick(Pick $pick): self
    {
        if ($this->picks->removeElement($pick)) {
            // set the owning side to null (unless already changed)
            if ($pick->getChampion() === $this) {
                $pick->setChampion(null);
            }
        }

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
            $matchup->setOpponent($this);
        }

        return $this;
    }

    public function removeMatchup(Matchup $matchup): self
    {
        if ($this->matchups->removeElement($matchup)) {
            // set the owning side to null (unless already changed)
            if ($matchup->getOpponent() === $this) {
                $matchup->setOpponent(null);
            }
        }

        return $this;
    }
}
