<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'project')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Job::class, cascade: ['persist', 'remove'])]
    private Collection $job;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Rapport::class, cascade: ['persist', 'remove'])]
    private Collection $rapport;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $date = null;

    public function __construct()
    {
        $this->job = new ArrayCollection();
        $this->rapport = new ArrayCollection();
    }

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJob(): Collection
    {
        return $this->job;
    }

    public function addJob(Job $job): static
    {
        if (!$this->job->contains($job)) {
            $this->job->add($job);
            $job->setProject($this);
        }

        return $this;
    }

    public function removeJob(Job $job): static
    {
        if ($this->job->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getProject() === $this) {
                $job->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, rapport>
     */
    public function getRapport(): Collection
    {
        return $this->rapport;
    }

    public function addRapport(rapport $rapport): static
    {
        if (!$this->rapport->contains($rapport)) {
            $this->rapport->add($rapport);
            $rapport->setProject($this);
        }

        return $this;
    }

    public function removeRapport(rapport $rapport): static
    {
        if ($this->rapport->removeElement($rapport)) {
            // set the owning side to null (unless already changed)
            if ($rapport->getProject() === $this) {
                $rapport->setProject(null);
            }
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
