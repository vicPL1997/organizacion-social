<?php

namespace App\Entity;

use App\Repository\ProyectosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProyectosRepository::class)]
class Proyectos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\ManyToOne(inversedBy: 'proyectos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sedes $sede = null;

    #[ORM\Column(length: 100)]
    private ?string $zonaActuacion = null;

    #[ORM\Column]
    private ?int $totalGasto = null;

    #[ORM\Column]
    private ?int $personalVinculado = null;

    #[ORM\Column]
    private ?int $totalParticipantes = null;

    #[ORM\Column]
    private ?int $totalVoluntarios = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaInicio = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaFinal = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'proyectos')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'proyecto', targetEntity: Gasto::class, orphanRemoval: true)]
    private Collection $gastos;

    #[ORM\Column(length: 50)]
    private ?string $Activo = null;

    #[ORM\OneToMany(mappedBy: 'proyecto', targetEntity: Ingreso::class)]
    private Collection $ingresos;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $ingresosTotales = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->gastos = new ArrayCollection();
        $this->ingresos = new ArrayCollection();
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

    public function getSede(): ?Sedes
    {
        return $this->sede;
    }

    public function setSede(?Sedes $sede): self
    {
        $this->sede = $sede;

        return $this;
    }

    public function getZonaActuacion(): ?string
    {
        return $this->zonaActuacion;
    }

    public function setZonaActuacion(string $zonaActuacion): self
    {
        $this->zonaActuacion = $zonaActuacion;

        return $this;
    }

    public function getTotalGasto(): ?int
    {
        return $this->totalGasto;
    }

    public function setTotalGasto(int $totalGasto): self
    {
        $this->totalGasto = $totalGasto;

        return $this;
    }

    public function getPersonalVinculado(): ?int
    {
        return $this->personalVinculado;
    }

    public function setPersonalVinculado(int $personalVinculado): self
    {
        $this->personalVinculado = $personalVinculado;

        return $this;
    }

    public function getTotalParticipantes(): ?int
    {
        return $this->totalParticipantes;
    }

    public function setTotalParticipantes(int $totalParticipantes): self
    {
        $this->totalParticipantes = $totalParticipantes;

        return $this;
    }

    public function getTotalVoluntarios(): ?int
    {
        return $this->totalVoluntarios;
    }

    public function setTotalVoluntarios(int $totalVoluntarios): self
    {
        $this->totalVoluntarios = $totalVoluntarios;

        return $this;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTimeInterface $fechaInicio): self
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getFechaFinal(): ?\DateTimeInterface
    {
        return $this->fechaFinal;
    }

    /**
     * @param \DateTimeInterface|null $fechaFinal
     */
    public function setFechaFinal(?\DateTimeInterface $fechaFinal): void
    {
        $this->fechaFinal = $fechaFinal;
    }



    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

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
            $gasto->setProyecto($this);
        }

        return $this;
    }

    public function removeGasto(Gasto $gasto): self
    {
        if ($this->gastos->removeElement($gasto)) {
            // set the owning side to null (unless already changed)
            if ($gasto->getProyecto() === $this) {
                $gasto->setProyecto(null);
            }
        }

        return $this;
    }

    public function getActivo(): ?string
    {
        return $this->Activo;
    }

    public function setActivo(string $Activo): self
    {
        $this->Activo = $Activo;

        return $this;
    }

    /**
     * @return Collection<int, Ingreso>
     */
    public function getIngresos(): Collection
    {
        return $this->ingresos;
    }

    public function addIngreso(Ingreso $ingreso): self
    {
        if (!$this->ingresos->contains($ingreso)) {
            $this->ingresos->add($ingreso);
            $ingreso->setProyecto($this);
        }

        return $this;
    }

    public function removeIngreso(Ingreso $ingreso): self
    {
        if ($this->ingresos->removeElement($ingreso)) {
            // set the owning side to null (unless already changed)
            if ($ingreso->getProyecto() === $this) {
                $ingreso->setProyecto(null);
            }
        }

        return $this;
    }

    public function getIngresosTotales(): ?string
    {
        return $this->ingresosTotales;
    }

    public function setIngresosTotales(string $ingresosTotales): self
    {
        $this->ingresosTotales = $ingresosTotales;

        return $this;
    }
}
