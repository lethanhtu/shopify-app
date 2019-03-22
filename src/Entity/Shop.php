<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopRepository")
 */
class Shop
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $shop_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $access_token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $script_tag_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $config;

    /**
     * @ORM\Column(type="datetime")
     */
    private $installed_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShopId(): ?string
    {
        return $this->shop_id;
    }

    public function setShopId(string $shop_id): self
    {
        $this->shop_id = $shop_id;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->access_token;
    }

    public function setAccessToken(?string $access_token): self
    {
        $this->access_token = $access_token;

        return $this;
    }

    public function getScriptTagId(): ?string
    {
        return $this->script_tag_id;
    }

    public function setScriptTagId(?string $script_tag_id): self
    {
        $this->script_tag_id = $script_tag_id;

        return $this;
    }

    public function getConfig(): ?string
    {
        return $this->config;
    }

    public function setConfig(?string $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function getInstalledDate(): ?\DateTimeInterface
    {
        return $this->installed_date;
    }

    public function setInstalledDate(\DateTimeInterface $installed_date): self
    {
        $this->installed_date = $installed_date;

        return $this;
    }

    public function getUpdatedDate(): ?\DateTimeInterface
    {
        return $this->updated_date;
    }

    public function setUpdatedDate(\DateTimeInterface $updated_date): self
    {
        $this->updated_date = $updated_date;

        return $this;
    }
}
