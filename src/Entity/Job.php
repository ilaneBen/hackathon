<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobRepository::class)]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'job')]
    private ?Project $project = null;

    #[ORM\Column]
    private ?bool $Resultat = null;

    #[ORM\Column]
    private array $detail = [];

    #[ORM\ManyToOne(inversedBy: 'job')]
    private ?Rapport $rapport = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function isResultat(): ?bool
    {
        return $this->Resultat;
    }

    public function setResultat(bool $Resultat): static
    {
        $this->Resultat = $Resultat;

        return $this;
    }

    public function getDetail(): array
    {
        return $this->detail;
    }

    public function setDetail(array $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getRapport(): ?Rapport
    {
        return $this->rapport;
    }

    public function setRapport(?Rapport $rapport): static
    {
        $this->rapport = $rapport;

        return $this;
    }
}
