<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
#[ORM\Table(name: '`Cart`')]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $reference;

    #[ORM\Column(type: 'string', length: 255)]
    private $fullName;

    #[ORM\Column(type: 'string', length: 255)]
    private $carrierName;

    #[ORM\Column(type: 'float')]
    private $carrierPrice;

    #[ORM\Column(type: 'text')]
    private $deliveryAddress;

    #[ORM\Column(type: 'boolean')]
    private $isPaid = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private $moreInformations;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\OneToMany(mappedBy: 'Carts', targetEntity: CartDetails::class)]
    private $CartDetails;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'Carts')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'integer')]
    private $quantity;

    #[ORM\Column(type: 'float')]
    private $subTotalHT;

    #[ORM\Column(type: 'float')]
    private $taxe;

    #[ORM\Column(type: 'float')]
    private $subTotalTTC;

    public function __construct()
    {
        $this->CartDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getCarrierName(): ?string
    {
        return $this->carrierName;
    }

    public function setCarrierName(string $carrierName): self
    {
        $this->carrierName = $carrierName;

        return $this;
    }

    public function getCarrierPrice(): ?float
    {
        return $this->carrierPrice*100;
    }

    public function setCarrierPrice(float $carrierPrice): self
    {
        $this->carrierPrice = $carrierPrice;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function isIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getMoreInformations(): ?string
    {
        return $this->moreInformations;
    }

    public function setMoreInformations(?string $moreInformations): self
    {
        $this->moreInformations = $moreInformations;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, CartDetails>
     */
    public function getCartDetails(): Collection
    {
        return $this->CartDetails;
    }

    public function addCartDetail(CartDetails $CartDetail): self
    {
        if (!$this->CartDetails->contains($CartDetail)) {
            $this->CartDetails[] = $CartDetail;
            $CartDetail->setCarts($this);
        }

        return $this;
    }

    public function removeCartDetail(CartDetails $CartDetail): self
    {
        if ($this->CartDetails->removeElement($CartDetail)) {
            // set the owning side to null (unless already changed)
            if ($CartDetail->getCarts() === $this) {
                $CartDetail->setCarts(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getsubTotalHT(): ?float
    {
        return $this->subTotalHT*100;
    }

    public function setsubTotalHT(float $subTotalHT): self
    {
        $this->subTotalHT = $subTotalHT;

        return $this;
    }

    public function getTaxe(): ?float
    {
        return $this->taxe*100;
    }

    public function setTaxe(float $taxe): self
    {
        $this->taxe = $taxe;

        return $this;
    }

    public function getSubTotalTTC(): ?float
    {
        return $this->subTotalTTC*100;
    }

    public function setSubTotalTTC(float $subTotalTTC): self
    {
        $this->subTotalTTC = $subTotalTTC;

        return $this;
    }
}
