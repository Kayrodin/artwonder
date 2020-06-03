<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WondArtRepository")
 */
class WondArt
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
    private $titulo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $media;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $historia;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etiquetas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MarcaAutor", inversedBy="wondarts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $marcaAutor;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comentario", mappedBy="wondArtComentado", orphanRemoval=true)
     */
    private $comentarios;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publicado;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha;

    public function __construct()
    {
        $this->comentarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(string $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getHistoria(): ?string
    {
        return $this->historia;
    }

    public function setHistoria(?string $historia): self
    {
        $this->historia = $historia;

        return $this;
    }

    public function getEtiquetas(): ?string
    {
        return $this->etiquetas;
    }

    public function setEtiquetas(?string $etiquetas): self
    {
        $this->etiquetas = $etiquetas;

        return $this;
    }

    public function getMarcaAutor(): ?MarcaAutor
    {
        return $this->marcaAutor;
    }

    public function setMarcaAutor(?MarcaAutor $marcaAutor): self
    {
        $this->marcaAutor = $marcaAutor;

        return $this;
    }

    /**
     * @return Collection|Comentario[]
     */
    public function getComentarios(): Collection
    {
        return $this->comentarios;
    }

    public function addComentario(Comentario $comentario): self
    {
        if (!$this->comentarios->contains($comentario)) {
            $this->comentarios[] = $comentario;
            $comentario->setWondArtComentado($this);
        }

        return $this;
    }

    public function removeComentario(Comentario $comentario): self
    {
        if ($this->comentarios->contains($comentario)) {
            $this->comentarios->removeElement($comentario);
            // set the owning side to null (unless already changed)
            if ($comentario->getWondArtComentado() === $this) {
                $comentario->setWondArtComentado(null);
            }
        }

        return $this;
    }

    public function getPublicado(): ?bool
    {
        return $this->publicado;
    }

    public function setPublicado(bool $publicado): self
    {
        $this->publicado = $publicado;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }
}
