<?php

declare(strict_types=1);

namespace openvk\Web\Models\Entities;

use Chandler\Database\DatabaseConnection;
use openvk\Web\Models\Repositories\{Users, Clubs};
use openvk\Web\Util\DateTime;
use openvk\Web\Models\Entities\User;
use openvk\Web\Models\RowModel;
use Nette\Database\Table\ActiveRow;

class CFeed extends RowModel
{
    protected $tableName = "customfeeds";

    use Traits\TOwnable;

    public function __construct(?ActiveRow $ar = null)
    {
        parent::__construct($ar);

        $this->relTable    = DatabaseConnection::i()->getContext()->table("customfeed_relations");
        $this->sourceTable = DatabaseConnection::i()->getContext()->table("customfeed_sources");
    }

    public function getId(): int
    {
        return $this->getRecord()->id;
    }

    public function getName(): string
    {
        return $this->getRecord()->name;
    }

    public function getOwner(): ?User
    {
        return (new Users())->get($this->getRecord()->owner);
    }

    public function getDescription(): string
    {
        return $this->getRecord()->description;
    }

    public function getCreationTime(): DateTime
    {
        return new DateTime($this->getRecord()->created);
    }

    public function getSources(): \Traversable
    {
        foreach ($this->sourceTable->where("feed", $this->getId()) as $source) {
            $id = $source->source;

            if ($id > 0) {
                $entity = (new Users)->get($id);
            } else {
                $entity = (new Clubs)->get(-$id);
            }

            if (!$entity) {
                continue;
            }

            yield $entity;
        }
    }

    public function addSource(RowModel $entity): bool
    {
        $sourceId = $entity->getId();

        if ($entity instanceof Club) {
            $sourceId *= -1;
        }

        if ($this->sourceTable->where([
            "feed" => $this->getId(),
            "source" => $sourceId,
        ])->count("*") > 0) {
            return false;
        }

        $this->sourceTable->insert([
            "feed" => $this->getId(),
            "source" => $sourceId,
        ]);

        return true;
    }

    public function getRelation(User $user): int 
    {
        $relation = $this->relTable->where([
            "user" => $user->getId(),
            "feed" => $this->getId(),
        ])->fetch();

        if (!$relation) {
            return -1;
        } 

        return (int) $relation->type;
    }

    public function addEditor(User $user): bool
    {
        if ($this->getRelation($user) === 0) {
            $this->relTable->update([
                "user" => $user->getId(),
                "feed" => $this->getId(),
                "type" => 1,
            ]); 
            return true;
        }

        return false;
    }

    public function isEditor(User $user): bool
    {
        if ($user === $this->getOwner()) {
            return true;
        }
        return $this->getRelation($user) === 1;
    }

    public function subscribe(User $user): bool
    {

        if ($this->getRelation($user) > -1 || $user === $this->getOwner()) {
            return false;
        }

        $this->relTable->insert([
            "user" => $user->getId(),
            "feed" => $this->getId(),
            "type" => 0,
        ]);

        return true;
    }

    public function unsubscribe(User $user): bool
    {
        if ($this->getRelation($user) === -1) {
            return false;
        }

        $this->relTable->where([
            "user" => $user->getId(),
            "feed" => $this->getId(),
        ])->delete();

        return true;
    }

}