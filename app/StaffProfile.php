<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    protected $connection = "auth_connection";

    protected $appends = ['permissions'];

    protected $append = ['permissions'];

    public function pillar()
    {
        return $this->belongsTo(Pillar::class, 'pillar');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section');
    }

    public function organisationUnit()
    {
        return $this->belongsTo(OrganisationUnit::class, 'organisation_unit');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category');
    }

    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class, 'appointment_type');
    }

    public function getPermissionsAttribute()
    {
        return collect(UserPermission::whereHas('user', function($query) {
            $query->where('email', $this->email);
        })->get())->map(function($d){ return $d->permission; })->values()->all();
    }
}
