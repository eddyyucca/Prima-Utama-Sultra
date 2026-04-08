<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPeriod extends Model
{
    protected $fillable = [
        'period_label', 'period_start', 'period_end',
        'working_days', 'uploaded_file', 'status', 'uploaded_by'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function records()
    {
        return $this->hasMany(SalaryRecord::class, 'period_id');
    }

    public function getTotalKaryawanAttribute()
    {
        return $this->records()->count();
    }

    public function getTotalGajiAttribute()
    {
        return $this->records()->sum('total_ditransfer');
    }
}
