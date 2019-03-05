<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $change_password;

    /**
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $hash;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $disabled_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_login_at;

    public function __construct() {
        $this->roles = new ArrayCollection();
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): void {
        $this->roles = $roles;
    }

    public function getSalt(): ?string {
        return null;
    }

    public function eraseCredentials(): void {

    }

    public function getCreatedAt(): \DateTimeInterface {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDisabledAt(): \DateTimeInterface {
        return $this->disabled_at;
    }

    public function setDisabledAt(\DateTimeInterface $disabled_at): self {
        $this->disabled_at = $disabled_at;

        return $this;
    }

    public function getLastLoginAt(): \DateTimeInterface {
        return $this->last_login_at;
    }

    public function setLastLoginAt(\DateTimeInterface $last_login_at): self {
        $this->last_login_at = $last_login_at;

        return $this;
    }

    public function getAvatar(): string {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self {
        $this->avatar = $avatar;

        return $this;
    }

    public function getChangePassword(): string {
        return $this->change_password;
    }

    public function setChangePassword(string $change_password): self {
        $this->change_password = $change_password;

        return $this;
    }

    public function getHash(): string {
        return $this->hash;
    }

    public function setHash(string $hash): self {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist(): void {
        $this->created_at = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate() {
        $this->updated_at = new \DateTime("now");
    }

}
