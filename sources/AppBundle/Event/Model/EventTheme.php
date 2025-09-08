<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Model\Repository\EventThemeRepository;
use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use CCMBenchmark\TingBundle\Schema\Column;
use CCMBenchmark\TingBundle\Schema\Table;

#[Table(name: 'afup_conference_theme', connection: 'main', database: '%env(DATABASE_NAME)%', repository: EventThemeRepository::class)]
class EventTheme implements NotifyPropertyInterface
{
    use NotifyProperty;
    #[Column(autoIncrement: true, primary: true)]
    private ?int $id = null;

    #[Column]
    private ?int $idForum = null;

    #[Column]
    private string $name;

    #[Column]
    private string $description;

    #[Column]
    private int $priority = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->propertyChanged('id', $this->id ?? null, $id);
        $this->id = $id;
    }

    public function getIdForum(): ?int
    {
        return $this->idForum;
    }

    public function setIdForum(?int $idForum): void
    {
        $this->propertyChanged('idForum', $this->idForum ?? null, $idForum);
        $this->idForum = $idForum;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->propertyChanged('name', $this->name ?? '', $name);
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->propertyChanged('description', $this->description ?? '', $description);
        $this->description = $description;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->propertyChanged('priority', $this->priority ?? 0, $priority);
        $this->priority = $priority;
    }
}
