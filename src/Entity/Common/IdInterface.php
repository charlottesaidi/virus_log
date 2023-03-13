<?php

namespace App\Entity\Common;

interface IdInterface
{
    public function getId(): ?int;

    public function setId(?int $id): void;
}