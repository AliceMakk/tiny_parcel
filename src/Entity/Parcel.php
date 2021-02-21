<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ParcelRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParcelRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Parcel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "The parcel name must be at least {{ limit }} characters long",
     *      maxMessage = "The parcel name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\PositiveOrZero
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $weight;

    /**
     * @Assert\PositiveOrZero
     * @ORM\Column(type="decimal", precision=10, scale=7, nullable=true)
     */
    private $volume;

    /**
     * @ORM\ManyToOne(targetEntity=Rate::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $rate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $quote;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $declared_value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(?string $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getRate(): ?Rate
    {
        return $this->rate;
    }

    public function setRate(?Rate $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getUpdatedDate(): ?\DateTimeInterface
    {
        return $this->updated_date;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PostUpdate
     */
    public function setUpdatedDate()
    {
        $this->updated_date = new \DateTime();
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->created_date;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedDate()
    {
        $this->created_date = new \DateTime();
    }

    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function setQuote(string $quote): self
    {
        $this->quote = $quote;

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'weight' => $this->getWeight(),
            'declaredValue' => $this->getDeclaredValue(),
            'volume' => $this->getVolume(),
            'quote' => $this->getQuote(),
            'price_model' => $this->getRate()->getUnit()->getName(),
        ];
    }

    public function getDeclaredValue(): ?int
    {
        return $this->declared_value;
    }

    public function setDeclaredValue(?int $declaredValue): self
    {
        $this->declared_value = $declaredValue;

        return $this;
    }
}
