<?php

namespace App\Traits;

use App\Models\KuviaModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;

trait Paginates
{
    use BuildsQueries;

    public static $page = 'page';
    public static $perPage = 'per_page';
    public static $search = 'search';
    public static $searchIn = 'search_in';
    public static $orderBy = 'order_by';
    public static $show = 'show';
    public static $hide = 'hide';

    /**
     * @param string|KuviaModel $class
     * @param Builder $query
     * @param array $params
     * @param callable|null $postProcessor
     * @return Paginator
     */
    public function paginator(string $class, $query, array $params, callable $postProcessor = null): Paginator
    {
        if ($searchValues = $params[self::$search] ?? null) {
            $searchColumns = $params[self::$searchIn] ?? $class::searchColumns();
            self::anyValueInAnyColumn($query, $searchValues, $searchColumns);
        }

        if ($orderByColumns = $params[self::$orderBy] ?? null) {
            foreach ($orderByColumns as $orderByColumn) {
                switch (substr($orderByColumn, 0, 1)) {
                    case '-':
                        $query->orderBy(substr($orderByColumn, 1), 'desc');
                        break;
                    case '+':
                        $query->orderBy(substr($orderByColumn, 1), 'asc');
                        break;
                    default:
                        $query->orderBy($orderByColumn);
                        break;
                }
            }
        }

        if ($perPage = $params[self::$perPage] ?? 15) {
            $paginator = $query->paginate(
                $perPage,
                ['*'],
                'page',
                $params[self::$page] ?? null
            );
        } else {
            $count = $query->count();
            $paginator = new Paginator($query->get(), $count, $count);
        }

        $showAttrs = $params[self::$show] ?? [];
        $hideAttrs = $params[self::$hide] ?? [];

        if ($postProcessor || $showAttrs || $hideAttrs) {
            $postProcessedItems = [];
            /** @var KuviaModel $item */
            foreach ($paginator->items() as $item) {
                if ($postProcessor) {
                    $item = $postProcessor($item);
                }

                if ($item instanceof Model) {
                    foreach ($showAttrs as $attr) {
                        if ($item->canMakeVisible($attr)) {
                            $item->makeVisible($attr);
                        }
                        if ($item->canAppend($attr)) {
                            $item->append($attr);
                        }
                    }
                    foreach ($hideAttrs as $attr) {
                        $item->makeHidden($attr);
                    }
                } else {
                    foreach ($hideAttrs as $attr) {
                        unset($item[$attr]);
                    }
                }
                $postProcessedItems[] = $item;
            }
            $paginator->setCollection(collect($postProcessedItems));
        }

        return $paginator;
    }
}
