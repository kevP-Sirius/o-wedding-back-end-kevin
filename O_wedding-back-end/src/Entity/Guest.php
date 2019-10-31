<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuestRepository")
 */
class Guest
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
    private $lastname;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phone_number;

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
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="guest" , cascade={"persist"})
     */
    private $projects;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $newsletter_is_active;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_coming;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $is_coming_with;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vegetarian_meal_number;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $meat_meal_number;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Type", inversedBy="guests")
     */
    private $type;

    public function __toString()
    {
        return $this->lastname;
    }

    public function __construct()
    {

        $this->projects = new ArrayCollection();
        $this->created_at = new \DateTime;
        $this->updated_at = new \DateTime;
        $this->is_active = true;
        $this->newsletter_is_active = true;
        $this->is_coming = false;
        $this->phone_number = 'non remplis';
        $this->email = 'non remplis';
        $this->vegetarian_meal_number = 0;
        $this->meat_meal_number = 0;
        $this->is_coming_with = 1;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?int $phone_number): self
    {
        $this->phone_number = $phone_number;

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

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addGuest($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            $project->removeGuest($this);
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

    public function getNewsletterIsActive(): ?bool
    {
        return $this->newsletter_is_active;
    }

    public function setNewsletterIsActive(?bool $newsletter_is_active): self
    {
        $this->newsletter_is_active = $newsletter_is_active;

        return $this;
    }

    public function getIsComing(): ?bool
    {
        return $this->is_coming;
    }

    public function setIsComing(?bool $is_coming): self
    {
        $this->is_coming = $is_coming;

        return $this;
    }
    
    public function getIsComingWith(): ?int
    {
        return $this->is_coming_with;
    }

    public function setIsComingWith(?int $is_coming_with): self
    {
        $this->is_coming_with = $is_coming_with;

        return $this;
    }

    public function getVegetarianMealNumber(): ?int
    {
        return $this->vegetarian_meal_number;
    }

    public function setVegetarianMealNumber(?int $vegetarian_meal_number): self
    {
        $this->vegetarian_meal_number = $vegetarian_meal_number;

        return $this;
    }

    public function getMeatMealNumber(): ?int
    {
        return $this->meat_meal_number;
    }

    public function setMeatMealNumber(?int $meat_meal_number): self
    {
        $this->meat_meal_number = $meat_meal_number;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }
}
