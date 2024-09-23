<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $Nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Description = null;

    #[ORM\Column]
    private ?int $Prix = null;

    /**
     * @var Collection<int, SousCategorie>
     */
    #[ORM\ManyToMany(targetEntity: SousCategorie::class, inversedBy: 'produits')]
    private Collection $SousCategories;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Image = null;

    #[ORM\Column]
    private ?int $stock = null;

    /**
     * @var Collection<int, HistoriqueProduit>
     */
    #[ORM\OneToMany(targetEntity: HistoriqueProduit::class, mappedBy: 'produit')]
    private Collection $historiqueProduits;

    public function __construct()
    {
        $this->SousCategories = new ArrayCollection();
        $this->historiqueProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->Prix;
    }

    public function setPrix(int $Prix): static
    {
        $this->Prix = $Prix;

        return $this;
    }

    /**
     * @return Collection<int, SousCategorie>
     */
    public function getSousCategories(): Collection
    {
        return $this->SousCategories;
    }

    public function addSousCategory(SousCategorie $sousCategory): static
    {
        if (!$this->SousCategories->contains($sousCategory)) {
            $this->SousCategories->add($sousCategory);
        }

        return $this;
    }

    public function removeSousCategory(SousCategorie $sousCategory): static
    {
        $this->SousCategories->removeElement($sousCategory);

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(?string $Image): static
    {
        $this->Image = $Image;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueProduit>
     */
    public function getHistoriqueProduits(): Collection
    {
        return $this->historiqueProduits;
    }

    public function addHistoriqueProduit(HistoriqueProduit $historiqueProduit): static
    {
        if (!$this->historiqueProduits->contains($historiqueProduit)) {
            $this->historiqueProduits->add($historiqueProduit);
            $historiqueProduit->setProduit($this);
        }

        return $this;
    }

    public function removeHistoriqueProduit(HistoriqueProduit $historiqueProduit): static
    {
        if ($this->historiqueProduits->removeElement($historiqueProduit)) {
            // set the owning side to null (unless already changed)
            if ($historiqueProduit->getProduit() === $this) {
                $historiqueProduit->setProduit(null);
            }
        }

        return $this;
    }
}
