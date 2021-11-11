<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPinVerfication extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function useremailverifications()
    {

        return $this->hasOne(UserEmailVerification::class, 'id', 'user_email_verifications_id');
    }
}
