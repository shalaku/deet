<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StatusBelongsToCastLiveCategory implements Rule
{
    protected $categoryId;

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function passes($attribute, $value)
    {
        return DB::table('statuses')
            ->where('status_category', 'cast_live')
            ->where('id', $this->categoryId)
            ->exists();
    }

    public function message()
    {
        return 'The selected status is invalid or does not belong to the specified category.';
    }
}
