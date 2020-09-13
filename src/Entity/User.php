<?php

declare(strict_types=1);

namespace App\Entity;

final class User
{
    /** @var int $id */
    private $id;

    /** @var string $name */
    private $name;

    /** @var string $email */
    private $email;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function updateName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function updateEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getData(): object
    {
        $user = new \stdClass();
        $user->id = $this->getId();
        $user->name = $this->getName();
        $user->email = $this->getEmail();

        return $user;
    }
}