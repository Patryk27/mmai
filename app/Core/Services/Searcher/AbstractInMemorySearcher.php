<?php

namespace App\Core\Services\Searcher;

use Illuminate\Support\Collection;

/**
 * This class provides a base for an in-memory-based searcher service.
 *
 * Since we have no unit tests covering in-memory searchers, this is basically
 * a dummy service doing nothing.
 */
abstract class AbstractInMemorySearcher implements SearcherInterface
{

    /**
     * @inheritDoc
     */
    public function search(string $search): void
    {
        unimplemented();
    }

    /**
     * @inheritDoc
     */
    public function filter(array $fields): void
    {
        unimplemented();
    }

    /**
     * @inheritDoc
     */
    public function orderBy(string $field, bool $ascending): void
    {
        unimplemented();
    }

    /**
     * @inheritDoc
     */
    public function forPage(int $page, int $perPage): void
    {
        unimplemented();
    }

    /**
     * @inheritDoc
     */
    public function get(): Collection
    {
        unimplemented();
    }

    /**
     * @inheritDoc
     */
    public function getCount(): int
    {
        unimplemented();
    }

}