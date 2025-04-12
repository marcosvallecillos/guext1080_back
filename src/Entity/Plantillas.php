<?php

namespace App\Entity;
use App\Repository\PlantillasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlantillasRepository::class)]
class Plantillas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: Contextos::class, inversedBy: 'plantillas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contextos $idcontext = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getIdcontext(): ?Contextos
    {
        return $this->idcontext;
    }

    public function setIdcontext(?Contextos $idcontext): static
    {
        $this->idcontext = $idcontext;
        return $this;
    }
}
