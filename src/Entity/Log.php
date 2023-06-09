<?php

namespace App\Entity;

use App\Entity\User;
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
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    private $type = null;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    private $ip = null;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    private $numberInfectedFile = 0;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
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
}