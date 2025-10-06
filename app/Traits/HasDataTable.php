<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Trait HasDataTable
 *
 * Provides reusable DataTable functionality for all controllers
 * Works seamlessly with the Vue DataTable component
 *
 * Usage in Controller:
 * ```php
 * use App\Traits\HasDataTable;
 *
 * class ProductController extends Controller
 * {
 *     use HasDataTable;
 *
 *     public function index(Request $request): JsonResponse
 *     {
 *         $query = Product::query();
 *
 *         return $this->dataTableResponse(
 *             query: $query,
 *             request: $request,
 *             resource: ProductResource::class,
 *             searchable: ['name', 'sku', 'description'],
 *             filterable: ['category_id', 'status'],
 *             defaultSort: 'created_at',
 *             defaultOrder: 'desc'
 *         );
 *     }
 * }
 * ```
 */
trait HasDataTable
{
    /**
     * Generate DataTable response with pagination, search, sort, and filters
     *
     * @param Builder $query The base Eloquent query
     * @param Request $request The HTTP request
     * @param string|null $resource The API Resource class for formatting
     * @param array $searchable Array of searchable columns
     * @param array $filterable Array of filterable columns
     * @param string $defaultSort Default sort column
     * @param string $defaultOrder Default sort order (asc/desc)
     * @param int $defaultPerPage Default items per page
     * @return JsonResponse
     */
    protected function dataTableResponse(
        Builder $query,
        Request $request,
        ?string $resource = null,
        array $searchable = [],
        array $filterable = [],
        string $defaultSort = 'id',
        string $defaultOrder = 'desc',
        int $defaultPerPage = 15
    ): JsonResponse {
        // Apply search
        if ($request->filled('search') && !empty($searchable)) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search, $searchable) {
                foreach ($searchable as $column) {
                    // Handle nested relations (e.g., 'category.name')
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

                // Handle array filters (multiple values)
                if (is_array($value)) {
                    $query->whereIn($filter, $value);
                } else {
                    $query->where($filter, $value);
                }
            }
        }

        // Apply date range filters (created_at, updated_at, etc.)
        if ($request->filled('date_from')) {
            $dateColumn = $request->input('date_column', 'created_at');
            $query->whereDate($dateColumn, '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $dateColumn = $request->input('date_column', 'created_at');
            $query->whereDate($dateColumn, '<=', $request->input('date_to'));
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', $defaultSort);
        $sortOrder = $request->input('sort_order', $defaultOrder);

        // Validate sort order
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc'])
            ? strtolower($sortOrder)
            : $defaultOrder;

        // Handle nested relation sorting (e.g., 'category.name')
        if (str_contains($sortBy, '.')) {
            [$relation, $relationColumn] = explode('.', $sortBy);
            $query->join(
                str_plural($relation),
                str_plural($relation) . '.id',
                '=',
                $query->getModel()->getTable() . '.' . $relation . '_id'
            )->orderBy(str_plural($relation) . '.' . $relationColumn, $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Get pagination parameters
        $perPage = $request->input('per_page', $defaultPerPage);
        $perPage = is_numeric($perPage) && $perPage > 0 && $perPage <= 100
            ? (int) $perPage
            : $defaultPerPage;

        // Paginate
        $paginator = $query->paginate($perPage);

        // Format response
        $data = $resource
            ? $resource::collection($paginator->items())
            : $paginator->items();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Generate a simple list response (without pagination)
     * Useful for dropdown options, etc.
     *
     * @param Builder $query The base Eloquent query
     * @param Request $request The HTTP request
     * @param string|null $resource The API Resource class
     * @param array $searchable Array of searchable columns
     * @param int $limit Maximum number of items to return
     * @return JsonResponse
     */
    protected function listResponse(
        Builder $query,
        Request $request,
        ?string $resource = null,
        array $searchable = [],
        int $limit = 100
    ): JsonResponse {
        // Apply search
        if ($request->filled('search') && !empty($searchable)) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search, $searchable) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        // Apply limit
        $results = $query->limit($limit)->get();

        // Format response
        $data = $resource
            ? $resource::collection($results)
            : $results;

        return response()->json([
            'data' => $data,
            'total' => $results->count()
        ]);
    }
}
