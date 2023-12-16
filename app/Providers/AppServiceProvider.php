<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('unique_in_array', function ($attribute, $value, $parameters, $validator) {
            $allFields = $validator->getData()[$parameters[0]];
        
            // Extract the key from the attribute
            $key = explode('.', $attribute);
            $key = end($key); // Get the last part, e.g., 'input_field_name'
        
            // Count the occurrences
            $count = 0;
            foreach ($allFields as $field) {
                if (isset($field[$key]) && $field[$key] == $value) {
                    $count++;
                }
            }
        
            return $count === 1;
        });
    }
}
