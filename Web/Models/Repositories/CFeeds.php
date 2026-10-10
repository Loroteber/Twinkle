<?php

declare(strict_types=1);

namespace openvk\Web\Models\Repositories;

use openvk\Web\Models\Entities\CFeed;
use Nette\Database\Table\ActiveRow;
use Chandler\Database\DatabaseConnection;

class CFeeds
{
    /* aggressive sql caching */
    private static $cache = [];

    private $cfeeds;
    private $context;
    private $editors;

    public function __construct()
    {
        $this->context = DatabaseConnection::i()->getContext();
        $this->cfeeds  = $this->context->table("customfeeds");
    }


    public function getCFeedById(int $id): ?CFeed
    {
        $cfeed = $this->cfeeds->where(["id" => $id])->fetch();

        if (!is_null($cfeed)) {
            return new CFeed($cfeed);
        } else {
            return null;
        }
    }
    
}