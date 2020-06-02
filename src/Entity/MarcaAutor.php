<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MarcaAutorRepository")
 */
class MarcaAutor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WondArt", mappedBy="marcaAutor", orphanRemoval=true)
     */
    private $wondarts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="marcas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $propietario;

    public function __construct()
    {
        $this->wondarts = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * @return Collection|WondArt[]
     */
    public function getWondarts(): Collection
    {
        return $this->wondarts;
    }

    public function addWondart(WondArt $wondart): self
    {
        if (!$this->wondarts->contains($wondart)) {
            $this->wondarts[] = $wondart;
            $wondart->setMarcaAutor($this);
        }

        return $this;
    }

    public function removeWondart(WondArt $wondart): self
    {
        if ($this->wondarts->contains($wondart)) {
            $this->wondarts->removeElement($wondart);
            // set the owning side to null (unless already changed)
            if ($wondart->getMarcaAutor() === $this) {
                $wondart->setMarcaAutor(null);
            }
        }

        return $this;
    }

    public function getPropietario(): ?Usuario
    {
        return $this->propietario;
    }

    public function setPropietario(?Usuario $propietario): self
    {
        $this->propietario = $propietario;

        return $this;
    }

    public function __toString()
    {
        return $this->getNombre();
    }
}
