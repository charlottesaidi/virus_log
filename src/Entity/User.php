<?php

namespace App\Entity;

Use App\Repository\UserRepository;
use App\Entity\Common\DatedInterface;
use App\Entity\Common\DatedTrait;
use App\Entity\Common\IdInterface;
use App\Entity\Common\IdTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("`user`")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields = {"email"},message ="Un utilisateur ayant cette adresse email existe déjà")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, DatedInterface, IdInterface
{
    use DatedTrait;
    use IdTrait;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    private $ip = null;

    /**
     * @ORM\Column
     */
    private ?array $roles = [];

    /**
     * @ORM\Column
     * @var string The hashed password
     */
    private $password = null;

    public function __construct() {
        $this->createdAt = new \DateTime();
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->ip;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->ip;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
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
}