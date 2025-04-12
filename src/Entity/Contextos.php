<?php

namespace App\Entity;

use App\Repository\ContextosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContextosRepository::class)]
class Contextos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $code_translate = null;

    /**
     * @var Collection<int, Variables>
     */
    #[ORM\ManyToMany(targetEntity: Variables::class, mappedBy: 'contextos')]
    #[ORM\JoinTable(name: 'contextos_variables')]
    private Collection $variables;

    /**
     * @var Collection<int, Plantillas>
     */
    #[ORM\OneToMany(targetEntity: Plantillas::class, mappedBy: 'idcontext')]
    private Collection $plantillas;

    public function __construct()
    {
        $this->variables = new ArrayCollection();
        $this->plantillas = new ArrayCollection();
    }

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

    public function getCodeTranslate(): ?string
    {
        return $this->code_translate;
    }

    public function setCodeTranslate(?string $code_translate): void
    {
        $this->code_translate = $code_translate;
    }

    public function getVariables(): Collection
    {
        return $this->variables;
    }

    public function setVariables(Collection $variables): void
    {
        $this->variables = $variables;
    }
    /**
     * @return Collection<int, Plantillas>
     */
    public function getPlantillas(): Collection
    {
        return $this->plantillas;
    }

    public function addPlantilla(Plantillas $plantilla): static
    {
        if (!$this->plantillas->contains($plantilla)) {
            $this->plantillas->add($plantilla);
            $plantilla->setIdcontext($this);
        }

        return $this;
    }

    public function removePlantilla(Plantillas $plantilla): static
    {
        if ($this->plantillas->removeElement($plantilla)) {
            // set the owning side to null (unless already changed)
            if ($plantilla->getIdcontext() === $this) {
                $plantilla->setIdcontext(null);
            }
        }

        return $this;
    }
}
