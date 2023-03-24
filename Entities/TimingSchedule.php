<?php

namespace Modules\Schedule\Entities;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimingSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'start', 'end', 'title'];


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    protected static function newFactory()
    {
        return \Modules\Schedule\Database\factories\TimingScheduleFactory::new ();
    }
}