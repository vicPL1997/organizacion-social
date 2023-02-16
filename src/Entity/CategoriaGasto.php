<?php

namespace App\Entity;

use App\Repository\CategoriaGastoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriaGastoRepository::class)]
class CategoriaGasto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\OneToMany(mappedBy: 'categoria', targetEntity: Gasto::class)]
    private Collection $gastos;

    public function __construct()
    {
        $this->gastos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection<int, Gasto>
     */
    public function getGastos(): Collection
    {
        return $this->gastos;
    }

    public function addGasto(Gasto $gasto): self
    {
        if (!$this->gastos->contains($gasto)) {
            $this->gastos->add($gasto);
            $gasto->setCategoria($this);
        }

        return $this;
    }

    public function removeGasto(Gasto $gasto): self
    {
        if ($this->gastos->removeElement($gasto)) {
            // set the owning side to null (unless already changed)
            if ($gasto->getCategoria() === $this) {
                $gasto->setCategoria(null);
            }
        }

        return $this;
    }
}
