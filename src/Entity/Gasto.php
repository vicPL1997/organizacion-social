<?php

namespace App\Entity;

use App\Repository\GastoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GastoRepository::class)]
class Gasto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importe = null;

    #[ORM\Column(length: 1000)]
    private ?string $descripcion = null;

    #[ORM\ManyToOne(inversedBy: 'gastos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategoriaGasto $categoria = null;

    #[ORM\ManyToOne(inversedBy: 'gastos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Proyectos $proyecto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImporte(): ?string
    {
        return $this->importe;
    }

    public function setImporte(string $importe): self
    {
        $this->importe = $importe;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getCategoria(): ?CategoriaGasto
    {
        return $this->categoria;
    }

    public function setCategoria(?CategoriaGasto $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getProyecto(): ?Proyectos
    {
        return $this->proyecto;
    }

    public function setProyecto(?Proyectos $proyecto): self
    {
        $this->proyecto = $proyecto;

        return $this;
    }
}
