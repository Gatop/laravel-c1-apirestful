<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    // Returns a collection with the response code
    protected function showAll(Collection $collection, $code = 200)
    {

        if ($collection->isEmpty()) {
            return $this->successResponse(['data' => $collection], $code);
        }

        $transformer = $collection->first()->transformer;

        // Filters the data based by query sent in the request
        $collection = $this->filterData($collection, $transformer);
        // Sorts the collection using the transform mapping of the attributes
        $collection = $this->sortData($collection, $transformer);
        // Paginates the collection before the transform and the response
        $collection = $this->paginate($collection);
        $collection = $this->transformData($collection, $transformer);

        // System to cache the response
        // Avoiding the DB OverCharge
        $collection = $this->cacheResponse($collection);

        return $this->successResponse($collection, $code);
    }

    // Returns an entity with the response code
    protected function showOne(Model $instance, $code = 200)
    {
        $transformer = $instance->transformer;

        $instance = $this->transformData($instance, $transformer);

        return $this->successResponse($instance, $code);
    }

    // Returns a message with the response code
    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    // Sorts a collections by a sent attribute as 'sort_by'
    protected function sortData($collection, $transformer)
    {
        // we use the helper request to get the value sort_by
        if (request()->has('sort_by')) {
            $attribute = $transformer::originalAttribute(request()->sort_by);

            $collection = $collection->sortBy($attribute);
            // Another way
            //$collection = $collection->sortBy->{$attribute};
        }

        return $collection;
    }

    // Filter a collection based on the parameters sent in the request as 'query'
    // Only if the value is equal
    protected function filterData(Collection $collection, $transformer)
    {
        foreach (request()->query() as $query => $value) {
            $attribute = $transformer::originalAttribute($query);
            if (isset($attribute, $value)) {
                $collection = $collection->where($attribute, $value);
            }
        }
        return $collection;
    }

    // Uses the transform class to map the attributes with new names
    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();
    }

    // Paginates a collection with the sent values
        protected function paginate(Collection $collection)
    {
        $rules = [
            'per_page' => 'integer|min:2|max:50'
        ];

        Validator::validate(request()->all(), $rules);
        // Paginator that resolves the actual page
        $page = LengthAwarePaginator::resolveCurrentPage();
        // Predifined length
        $perPage = 15;

        // Set the parameter of the number of the items if is set on the request
        if (request()->has('per_page')) {
            $perPage = (int) request()->per_page;
        }

        // Splits the collection according with the received parameters
        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        // Instance of the paginator
        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        // Allowing the others parameters passed in the request (sortby, filters)
        $paginated->appends(request()->all());
        return $paginated;
    }

    // Cache of the response
    protected function cacheResponse($data)
    {
        // Get the actual url
        $url = request()->url();

        // Getting the sent options in the response
        $queryParams = request()->query();
        // Orders an array depending on the key
        ksort($queryParams);
        $queryString = http_build_query($queryParams);

        // Building the object to cache (including the parameters)
        $fullUrl = "{$url}?{$queryString}";

        // Method to review if is necessary to create a cache
        // Second parameter is the time (30 seconds)
        return Cache::remember($fullUrl, 30/60, function() use($data) {
            return $data;
        });
    }
}
