<?php

namespace Yml\Models;

class Category
{
    private $id;
    private $name;
    /** @var int|null */
    private $parentId = null;
    
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function parentId(): ?int
    {
        return $this->parentId;
    }
}
