<?php

namespace App\Entity;

use App\Repository\VariablesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VariablesRepository::class)]
class Variables
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    /**
     * @var Collection<int, Contextos>
     */
    #[ORM\ManyToMany(targetEntity: Contextos::class, inversedBy: 'variables')]
    #[ORM\JoinTable(name: 'contextos_variables')]
    private Collection $contextos;

    public function __construct()
    {
        $this->contextos = new ArrayCollection();
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

    /*
    public function getContextoVariables(): Collection
    {
        return $this->contextoVariables;
    }

    public function addContextoVariable(ContextoVariable $contextoVariable): static
    {
        if (!$this->contextoVariables->contains($contextoVariable)) {
            $this->contextoVariables->add($contextoVariable);
            $contextoVariable->addIdvariable($this);
        }

        return $this;
    }

    public function removeContextoVariable(ContextoVariable $contextoVariable): static
    {
        if ($this->contextoVariables->removeElement($contextoVariable)) {
            $contextoVariable->removeIdvariable($this);
        }

        return $this;
    }
    */
}
