<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $transformer)
    {
        $transformedInput = [];

        // Gets all the inputs and transform 
        foreach ($request->request->all() as $input => $value) {
            $transformedInput[$transformer::originalAttribute($input)] = $value;
        }

        // Replacing the old inputs with the new ones
        $request->replace($transformedInput);

        // Getting the response to set the new attributes names
        $response = $next($request);

        // Check if the response is an exception to set the correct names of the attributes
        if (isset($response->exception) && $response->exception instanceof ValidationException) {
            $data = $response->getData();
            $transformedErrors = [];

            // Checks all the errors messages and replacing again
            foreach ($data->error as $field => $error) {
                // Gets the correct name from the transformer
                $transformedField = $transformer::transformedAttribute($field);
                $transformedErrors[$transformedField] = str_replace($field, $transformedField, $error);
            }

            // Set the new data errors
            $data->error = $transformedErrors;
            $response->setData($data);
        }

        // Return the final response
        return $response;
    }
}
