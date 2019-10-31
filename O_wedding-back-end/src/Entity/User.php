<?php

namespace App\Entity;

use DateTime;
use Serializable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $session_duration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     * 
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
     * @ORM\OneToOne(targetEntity="App\Entity\Project", mappedBy="user", cascade={"remove","persist"})
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $role;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_connect;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_alert_active;

    public function __toString()
    {
        return $this->username;
    }
    
    public function __construct()
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $this->session_duration = $timestamp+3600;
        $this->created_at = new \DateTime;
        $this->updated_at = new \DateTime;
        $this->is_active = true;
        $this->is_alert_active = true;
        $this->is_connect = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSessionDuration(): ?int
    {
        return $this->session_duration;
    }

    public function setSessionDuration(?int $session_duration): self
    {
        $this->session_duration = $session_duration;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }


    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getProject(): ?Project
    {   
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $project === null ? null : $this;
        if ($newUser !== $project->getUser()) {
            $project->setUser($newUser);
        }

        return $this;
    }
    public function getRoles()
    {
        return array($this->getRole()->getRoleString());
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }


    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getIsConnect(): ?bool
    {
        return $this->is_connect;
    }

    public function setIsConnect(?bool $is_connect): self
    {
        $this->is_connect = $is_connect;

        return $this;
    }

    public function getIsAlertActive(): ?bool
    {
        return $this->is_alert_active;
    }

    public function setIsAlertActive(?bool $is_alert_active): self
    {
        $this->is_alert_active = $is_alert_active;

        return $this;
    }
}

