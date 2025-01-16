<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesSlug
{
    /**
     * Generate a unique slug for a given model and field.
     *
     * @param  string $title
     * @param  string $modelClass
     * @param  string $field
     * @return string
     */
    public static function generateSlug($title, $modelClass, $field = 'slug')
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        // Check for duplicates in the specified model
        while ($modelClass::where($field, $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
