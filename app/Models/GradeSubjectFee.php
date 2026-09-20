<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeSubjectFee extends Model
{
    protected $fillable = ['grade_id', 'subject_id', 'fee'];

    protected $casts = [
        'fee' => 'decimal:2'
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
