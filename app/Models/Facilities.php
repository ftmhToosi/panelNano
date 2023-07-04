<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facilities extends Model
{
    use HasFactory;

    public function request()
    {
        return $this->belongsTo(Requests::class);
    }

    public function introduction()
    {
        return $this->hasMany(Introduction::class);
    }

    public function shareholder()
    {
        return $this->hasMany(Shareholder::class);
    }

    public function part2()
    {
        return $this->hasMany(Part2::class);
    }

    public function board()
    {
        return $this->hasMany(Board::class);
    }

    public function residence()
    {
        return $this->hasMany(Residence::class);
    }

    public function manpower()
    {
        return$this->hasMany(Manpower::class);
    }

    public function educational()
    {
        return $this->hasMany(Educational::class);
    }

    public function place()
    {
        return $this->hasMany(Place::class);
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function bank()
    {
        return $this->hasMany(Bank::class);
    }

    public function active_f()
    {
        return $this->hasMany(ActiveF::class);
    }

    public function active_w()
    {
        return $this->hasMany(ActiveW::class);
    }

    public function benefit()
    {
        return $this->hasMany(Benefit::class);
    }

    public function asset()
    {
        return $this->hasMany(Asset::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approvals::class);
    }

    public function contract()
    {
        return $this->hasMany(Contract::class);
    }

    public function pledge()
    {
        return $this->hasMany(Pledge::class);
    }

    public function estate()
    {
        return $this->hasMany(Estate::class);
    }

    public function finish()
    {
        return $this->hasMany(Finish::class);
    }
}
