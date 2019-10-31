<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $deadline;

    /**
     * @ORM\Column(type="integer")
     */
    private $forecast_budget;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $current_budget;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="project",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Department", inversedBy="projects" ,cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $department;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Provider", inversedBy="projects" , cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $provider;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Guest", inversedBy="projects", cascade={"remove","persist"})
     */
    private $guest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->provider = new ArrayCollection();
        $this->guest = new ArrayCollection();
        $this->created_at = new \DateTime;
        $this->updated_at = new \DateTime;
        $this->is_active = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDeadline(): ?string
    {
        return $this->deadline;
    }

    public function setDeadline(string $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getForecastBudget(): ?int
    {
        return $this->forecast_budget;
    }

    public function setForecastBudget(int $forecast_budget): self
    {
        $this->forecast_budget = $forecast_budget;

        return $this;
    }

    public function getCurrentBudget(): ?int
    {
        return $this->current_budget;
    }

    public function setCurrentBudget(?int $current_budget): self
    {
        $this->current_budget = $current_budget;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    /**
     * @return Collection|Provider[]
     */
    public function getProvider(): Collection
    {
        return $this->provider;
    }

    public function addProvider(Provider $provider): self
    {
        if (!$this->provider->contains($provider)) {
            $this->provider[] = $provider;
        }

        return $this;
    }

    public function removeProvider(Provider $provider): self
    {
        if ($this->provider->contains($provider)) {
            $this->provider->removeElement($provider);
        }

        return $this;
    }

    /**
     * @return Collection|Guest[]
     */
    public function getGuest(): Collection
    {
        return $this->guest;
    }

    public function addGuest(Guest $guest): self
    {
        if (!$this->guest->contains($guest)) {
            $this->guest[] = $guest;
        }

        return $this;
    }

    public function removeGuest(Guest $guest): self
    {
        if ($this->guest->contains($guest)) {
            $this->guest->removeElement($guest);
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

   
}
