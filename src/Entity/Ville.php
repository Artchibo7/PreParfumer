<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de la ville ne peut pas être vide.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom de la ville ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s'-]+$/u",
        message: 'Le nom de la ville ne doit contenir que des lettres.'
    )]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Les frais de port ne peuvent pas être vides.')]
    #[Assert\Positive(message: 'Les frais de port doivent être un nombre positif.')]
    private ?float $fraisDePort = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'ville')]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'); // Sanitization

        return $this;
    }

    public function getFraisDePort(): ?float
    {
        return $this->fraisDePort;
    }

    public function setFraisDePort(float $fraisDePort): static
    {
        $this->fraisDePort = $fraisDePort;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setVille($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getVille() === $this) {
                $commande->setVille(null);
            }
        }

        return $this;
    }
}
