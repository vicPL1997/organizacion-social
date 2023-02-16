<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use DateTime;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidos = null;

    #[ORM\Column(length: 50)]
    private ?string $sexo = null;

    #[ORM\Column]
    private ?int $edad = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaNacimiento = null;

    #[ORM\Column(length: 255)]
    private ?string $nacionalidad = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column(length: 100)]
    private ?string $estadoCivil = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $discapacidad = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaAlta = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fechaBaja = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $documentacionLegal = null;

    #[ORM\Column(length: 255)]
    private ?string $rol = null;

    #[ORM\Column(length: 255)]
    private ?string $comunidadAutonoma = null;

    #[ORM\OneToOne(mappedBy: 'administradorSede', cascade: ['persist', 'remove'])]
    private ?Sedes $sede = null;

    #[ORM\ManyToMany(targetEntity: Proyectos::class, mappedBy: 'users')]
    private Collection $proyectos;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dedicacionEmpleado = null;

    #[ORM\Column(nullable: true)]
    private ?int $sueldoEmpleado = null;

    #[ORM\Column(nullable: true)]
    private ?int $tieneSedeAdministrada = null;

    public function __construct()
    {
        $this->proyectos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(int $edad): self
    {
        $this->edad = $edad;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(\DateTimeInterface $fechaNacimiento): self
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }
    public function setFechaNacimiento2(int $fechaNacimiento): self
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    public function getNacionalidad(): ?string
    {
        return $this->nacionalidad;
    }

    public function setNacionalidad(string $nacionalidad): self
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getEstadoCivil(): ?string
    {
        return $this->estadoCivil;
    }

    public function setEstadoCivil(string $estadoCivil): self
    {
        $this->estadoCivil = $estadoCivil;

        return $this;
    }

    public function getDiscapacidad(): ?string
    {
        return $this->discapacidad;
    }

    public function setDiscapacidad(?string $discapacidad): self
    {
        $this->discapacidad = $discapacidad;

        return $this;
    }

    public function getFechaAlta(): ?\DateTimeInterface
    {
        return $this->fechaAlta;
    }

    public function setFechaAlta(\DateTimeInterface $fechaAlta): self
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    public function getFechaBaja(): ?\DateTimeInterface
    {
        return $this->fechaBaja;
    }

    public function setFechaBaja(?\DateTimeInterface $fechaBaja): self
    {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    public function getDocumentacionLegal(): ?string
    {
        return $this->documentacionLegal;
    }

    public function setDocumentacionLegal(?string $documentacionLegal): self
    {
        $this->documentacionLegal = $documentacionLegal;

        return $this;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(string $rol): self
    {
        $this->rol = $rol;

        return $this;
    }

    public function getComunidadAutonoma(): ?string
    {
        return $this->comunidadAutonoma;
    }

    public function setComunidadAutonoma(string $comunidadAutonoma): self
    {
        $this->comunidadAutonoma = $comunidadAutonoma;

        return $this;
    }
    function obtener_edad_segun_fecha($fechaNacimiento)
    {
        $ahora = new DateTime(date("Y-m-d"));
        $diferencia = $ahora->diff($fechaNacimiento);
        return $diferencia->format("%y");
    }

    public function getSede(): ?Sedes
    {
        return $this->sede;
    }

    public function setSede(?Sedes $sede): self
    {
        // unset the owning side of the relation if necessary
        if ($sede === null && $this->sede !== null) {
            $this->sede->setAdministradorSede(null);
        }

        // set the owning side of the relation if necessary
        if ($sede !== null && $sede->getAdministradorSede() !== $this) {
            $sede->setAdministradorSede($this);
        }

        $this->sede = $sede;

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
            $proyecto->addUser($this);
        }

        return $this;
    }

    public function removeProyecto(Proyectos $proyecto): self
    {
        if ($this->proyectos->removeElement($proyecto)) {
            $proyecto->removeUser($this);
        }

        return $this;
    }

    public function getDedicacionEmpleado(): ?string
    {
        return $this->dedicacionEmpleado;
    }

    public function setDedicacionEmpleado(?string $dedicacionEmpleado): self
    {
        $this->dedicacionEmpleado = $dedicacionEmpleado;

        return $this;
    }

    public function getSueldoEmpleado(): ?int
    {
        return $this->sueldoEmpleado;
    }

    public function setSueldoEmpleado(?int $sueldoEmpleado): self
    {
        $this->sueldoEmpleado = $sueldoEmpleado;

        return $this;
    }

    public function getTieneSedeAdministrada(): ?int
    {
        return $this->tieneSedeAdministrada;
    }

    public function setTieneSedeAdministrada(?int $tieneSedeAdministrada): self
    {
        $this->tieneSedeAdministrada = $tieneSedeAdministrada;

        return $this;
    }
    public function __toString()
    {
        $string =$this->getNombre(). ' ' . $this->getApellidos().'-'.$this->getComunidadAutonoma();
        return $string;
    }

}
