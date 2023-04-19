<?php

namespace App;

use Carbon\Carbon;
use App\Lib\Traits\LeaveTrait;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use LeaveTrait;

    protected $fillable = [
        'personal_id', 'start', 'end', 'days', 'delegated_to', 'status', 'name'
    ];

    protected $appends = [
        "start_text", "duration", "end_text", "in_days"
    ];

    public function getConflictsAttribute() {
        return $this->getPossibleConflicts();
    }

    public function getStartTextAttribute()
    {
        try {

            if(now()->startOfDay()->greaterThan(Carbon::parse($this->end))) {
                return Carbon::parse($this->start)->format("d/M/Y");
            }

            $diffInDays = now()->startOfDay()->diffInDays(Carbon::parse($this->start));

            if($diffInDays  == 0) {
                return "Today";
            } elseif($diffInDays  == 1) {
                return "Tomorrow";
            } elseif($diffInDays <= 7) {
                return "In $diffInDays day(s)";
            } else {
                return Carbon::parse($this->start)->format("d/M/Y");
            }
        } catch (\Exception $exception) {
            return Carbon::parse($this->start)->format("d/M/Y");
        }
    }

    public function getEndTextAttribute()
    {
        try {
            return Carbon::parse($this->end)->format("d/M/Y");
        } catch (\Exception $exception) {
            return "";
        }
    }

    public function getDurationAttribute()
    {
        try {

            $diffInDays = Carbon::parse($this->start)->startOfDay()
                                        ->diffInDays(Carbon::parse($this->end)
                                        ->startOfDay()) + 1;
            return $diffInDays . " Day(s)";
        } catch (\Exception $exception) {
            return "N/A";
        }
    }

    public function getInDaysAttribute()
    {
        try {
            $diffInDays = now()->startOfDay()->diffInDays(Carbon::parse($this->start));

            if($diffInDays  == 0) {
                return "Today";
            } elseif($diffInDays  == 1) {
                return "Tomorrow";
            } else {
                return "in $diffInDays days";
            }
        } catch (\Exception $ex) { return 'N/A Days'; }
    }

    public function staff()
    {
        return $this->belongsTo(StaffProfile::class, 'personal_id', 'personal_id');
    }

    public function delegatedPeople()
    {
        return $this->hasMany(LeaveDelegation::class);
    }
}
