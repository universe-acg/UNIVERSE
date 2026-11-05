<?php
namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\MappedSuperclass]
abstract class UserIdentity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(length: 180, unique: true)]
    protected $username = '';
    #[ORM\Column(length: 180, unique: true)]
    protected $usernameCanonical = '';
    #[ORM\Column(length: 180, unique: true)]
    protected $email = '';
    #[ORM\Column(length: 180, unique: true)]
    protected $emailCanonical = '';
    #[ORM\Column(length: 255)]
    protected $password = '';
    #[ORM\Column(type: 'json')]
    protected $roles = [];
    #[ORM\Column(type: 'boolean')]
    protected $enabled = true;
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $lastLogin;
    #[ORM\Column(length: 64, nullable: true, unique: true)]
    protected $confirmationToken;
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $passwordRequestedAt;
    protected $plainPassword;

    public function __construct() {}
    public function getUserIdentifier(): string { return (string) $this->username; }
    public function getUsername(): string { return (string) $this->username; }
    public function setUsername($value): static { $this->username = $value; $this->usernameCanonical = mb_strtolower($value); return $this; }
    public function getUsernameCanonical(): string { return $this->usernameCanonical; }
    public function setUsernameCanonical($value): static { $this->usernameCanonical = $value; return $this; }
    public function getEmail(): string { return (string) $this->email; }
    public function setEmail($value): static { $this->email = $value; $this->emailCanonical = mb_strtolower($value); return $this; }
    public function getEmailCanonical(): string { return $this->emailCanonical; }
    public function setEmailCanonical($value): static { $this->emailCanonical = $value; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword($value): static { $this->password = $value; return $this; }
    public function getPlainPassword(): ?string { return $this->plainPassword; }
    public function setPlainPassword($value): static { $this->plainPassword = $value; return $this; }
    public function getRoles(): array { return array_values(array_unique([...$this->roles, 'ROLE_USER'])); }
    public function setRoles(array $roles): static { $this->roles = array_values(array_unique($roles)); return $this; }
    public function addRole($role): static { $this->roles = array_values(array_unique([...$this->roles, $role])); return $this; }
    public function removeRole($role): static { $this->roles = array_values(array_diff($this->roles, [$role])); return $this; }
    public function hasRole($role): bool { return in_array($role, $this->getRoles(), true); }
    public function isSuperAdmin(): bool { return $this->hasRole('ROLE_ADMINISTRATOR'); }
    public function setSuperAdmin($value): static { return $value ? $this->addRole('ROLE_ADMINISTRATOR') : $this->removeRole('ROLE_ADMINISTRATOR'); }
    public function isEnabled(): bool { return $this->enabled; }
    public function setEnabled($value): static { $this->enabled = (bool) $value; return $this; }
    public function getLastLogin(): ?\DateTimeInterface { return $this->lastLogin; }
    public function setLastLogin($value): static { $this->lastLogin = $value; return $this; }
    public function getConfirmationToken(): ?string { return $this->confirmationToken; }
    public function setConfirmationToken($value): static { $this->confirmationToken = $value; return $this; }
    public function getPasswordRequestedAt(): ?\DateTimeInterface { return $this->passwordRequestedAt; }
    public function setPasswordRequestedAt($value): static { $this->passwordRequestedAt = $value; return $this; }
    public function getSalt(): ?string { return null; }
    public function eraseCredentials(): void { $this->plainPassword = null; }
    public function __toString(): string { return (string) $this->username; }
}
