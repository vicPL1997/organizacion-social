<?php

namespace App\Entity;

use App\Repository\SedesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SedesRepository::class)]
class Sedes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $localizacion = null;

    #[ORM\OneToOne(inversedBy: 'sede', cascade: ['persist', 'remove'])]
    private ?User $administradorSede = null;

    #[ORM\OneToMany(mappedBy: 'sede', targetEntity: Proyectos::class, orphanRemoval: true)]
    private Collection $proyectos;

    public function __construct()
    {
        $this->proyectos = new ArrayCollection();
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

    public function getLocalizacion(): ?string
    {
        return $this->localizacion;
    }

    public function setLocalizacion(string $localizacion): self
    {
        $this->localizacion = $localizacion;

        return $this;
    }

    public function getAdministradorSede(): ?User
    {
        return $this->administradorSede;
    }

    public function setAdministradorSede(?User $administradorSede): self
    {
        $this->administradorSede = $administradorSede;

        return $this;
    }

    /**
     * @return Collection<int, Proyectos>
     */
    public function getProyectos(): Collection
    {
        return $this->proyectos;
    }

    public function addProyecto(Proyectos $proyecto): self
    {
        if (!$this->proyectos->contains($proyecto)) {
            $this->proyectos->add($proyecto);
            $proyecto->setSede($this);
        }

        return $this;
    }

    public function removeProyecto(Proyectos $proyecto): self
    {
        if ($this->proyectos->removeElement($proyecto)) {
            // set the owning side to null (unless already changed)
            if ($proyecto->getSede() === $this) {
                $proyecto->setSede(null);
            }
        }

        return $this;
    }
}
