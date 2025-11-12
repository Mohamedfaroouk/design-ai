<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Trait HasDataTableInertia
 *
 * Provides Inertia-compatible DataTable functionality
 */
trait HasDataTableInertia
{
    /**
     * Generate Inertia DataTable response
     */
    protected function inertiaDataTable(
        string $page,
        Builder $query,
        Request $request,
        ?string $resource = null,
        array $searchable = [],
        array $filterable = [],
        string $defaultSort = 'id',
        string $defaultOrder = 'desc',
        int $defaultPerPage = 15,
        array $additionalProps = []
    ): InertiaResponse {
        // Apply search
        if ($request->filled('search') && !empty($searchable)) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search, $searchable) {
                foreach ($searchable as $column) {
                    if (str_contains($column, '.')) {
                        [$relation, $relationColumn] = explode('.', $column);
                        $q->orWhereHas($relation, function ($subQuery) use ($relationColumn, $search) {
                            $subQuery->where($relationColumn, 'like', "%{$search}%");
                        });
                    } else {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        // Apply filters
        foreach ($filterable as $filter) {
            if ($request->filled($filter)) {
                $value = $request->input($filter);
                
                // Handle special filter types
                if ($filter === 'role') {
                    // Filter by role relationship
                    if (is_array($value)) {
                        $query->whereHas('roles', function ($q) use ($value) {
                            $q->whereIn('id', $value);
                        });
                    } else {
                        $query->whereHas('roles', function ($q) use ($value) {
                            $q->where('id', $value);
                        });
                    }
                } else {
                    // Standard filter
                    if (is_array($value)) {
                        $query->whereIn($filter, $value);
                    } else {
                        $query->where($filter, $value);
                    }
                }
            }
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', $defaultSort);
        $sortOrder = $request->input('sort_order', $defaultOrder);
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc'])
            ? strtolower($sortOrder)
            : $defaultOrder;

        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->input('per_page', $defaultPerPage);
        $perPage = is_numeric($perPage) && $perPage > 0 && $perPage <= 100
            ? (int) $perPage
            : $defaultPerPage;

        $paginator = $query->paginate($perPage)->withQueryString();

        // Format data - Convert ResourceCollection to array for Inertia
        if ($resource) {
            $collection = $resource::collection($paginator->items());
            // Resolve collection to array - toArray() returns ['data' => [...]] format
            $resolved = $collection->resolve($request);
            // If resolved is an array with 'data' key, extract it, otherwise use as is
            $data = is_array($resolved) && isset($resolved['data']) ? $resolved['data'] : $resolved;
        } else {
            $data = $paginator->items();
        }

        // Return Inertia response
        return Inertia::render($page, array_merge([
            'items' => $data,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'filters' => [
                'search' => $request->input('search', ''),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ], $additionalProps));
    }
}
