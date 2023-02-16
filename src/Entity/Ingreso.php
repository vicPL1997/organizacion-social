<?php

namespace App\Entity;

use App\Repository\IngresoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngresoRepository::class)]
class Ingreso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tipo = null;

    #[ORM\ManyToOne(inversedBy: 'ingresos')]
    private ?Proyectos $proyecto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombreEmisor = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cantidad = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

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

    public function getNombreEmisor(): ?string
    {
        return $this->nombreEmisor;
    }

    public function setNombreEmisor(?string $nombreEmisor): self
    {
        $this->nombreEmisor = $nombreEmisor;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCantidad(): ?string
    {
        return $this->cantidad;
    }

    /**
     * @param string|null $cantidad
     */
    public function setCantidad(?string $cantidad): void
    {
        $this->cantidad = $cantidad;
    }


}
