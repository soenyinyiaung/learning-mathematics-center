<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    protected $fillable = ['name'];

    public function gradeSubjectFees()
    {
        return $this->hasMany(GradeSubjectFee::class);
    }
}
