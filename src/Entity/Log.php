<?php

namespace App\Entity;

use App\Entity\Common\DatedInterface;
use App\Entity\Common\DatedTrait;
use App\Entity\Common\IdInterface;
use App\Entity\Common\IdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("`log`")
 * @ORM\Entity(repositoryClass=LogRepository::class)
 */
class Log implements DatedInterface, IdInterface
{
    use DatedTrait;
    use IdTrait;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    private $type = null;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    private $ip = null;

    /**
     * @ORM\Column
     */
    private $numberInfectedFile = 0;

    /**
     * @ORM\Column
     */
    private $key = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null $type
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return null
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param null $ip
     */
    public function setIp($ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumberInfectedFile(): int
    {
        return $this->numberInfectedFile;
    }

    /**
     * @param int $numberInfectedFile
     */
    public function setNumberInfectedFile(int $numberInfectedFile): self
    {
        $this->numberInfectedFile = $numberInfectedFile;

        return $this;
    }

    /**
     * @return null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @param null $key
     */
    public function setKey(?string $key): self
    {
        $this->key = $key;

        return $this;
    }
}