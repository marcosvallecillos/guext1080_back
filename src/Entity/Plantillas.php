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

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $code = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $data = null;

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

    public function getData(): array
    {
        return json_decode($this->data ?? '{}', true);
    }

    public function setData(array $data): static
    {
        $this->data = json_encode($data);
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
