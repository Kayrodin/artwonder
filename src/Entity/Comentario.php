<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ComentarioRepository")
 */
class Comentario
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
    private $autor;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comentario", inversedBy="respuestas")
     */
    private $responde;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comentario", mappedBy="responde")
     */
    private $respuestas;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WondArt", inversedBy="comentarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wondArtComentado;

    public function __construct()
    {
        $this->respuestas = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAutor(): ?string
    {
        return $this->autor;
    }

    public function setAutor(string $autor): self
    {
        $this->autor = $autor;

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

    public function getResponde(): ?self
    {
        return $this->responde;
    }

    public function setResponde(?self $responde): self
    {
        $this->responde = $responde;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getRespuestas(): Collection
    {
        return $this->respuestas;
    }

    public function addRespuesta(self $respuesta): self
    {
        if (!$this->respuestas->contains($respuesta)) {
            $this->respuestas[] = $respuesta;
            $respuesta->setResponde($this);
        }

        return $this;
    }

    public function removeRespuesta(self $respuesta): self
    {
        if ($this->respuestas->contains($respuesta)) {
            $this->respuestas->removeElement($respuesta);
            // set the owning side to null (unless already changed)
            if ($respuesta->getResponde() === $this) {
                $respuesta->setResponde(null);
            }
        }

        return $this;
    }

    public function getWondArtComentado(): ?WondArt
    {
        return $this->wondArtComentado;
    }

    public function setWondArtComentado(?WondArt $wondArtComentado): self
    {
        $this->wondArtComentado = $wondArtComentado;

        return $this;
    }


}
