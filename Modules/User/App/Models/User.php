<?php

namespace Modules\User\App\Models;

use Spatie\Activitylog\LogOptions;
use Modules\School\App\Models\School;
use Spatie\Permission\Traits\HasRoles;
use Modules\Student\App\Models\Student;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Teacher\App\Models\TeacherProfile;
use Illuminate\Contracts\Auth\CanResetPassword;
use Modules\Notification\App\Models\Notification;
use Modules\User\App\Notifications\UserVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail;

class User extends Authenticatable implements JWTSubject, MustVerifyEmailContract,  CanResetPassword
{
    use HasFactory, HasRoles, LogsActivity, MustVerifyEmail,  CanResetPasswordTrait, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'role', 'is_active', 'image', 'school_id', 'fcm_token', 'email_verified_at', 'job_title'];
    protected $hidden = ['password', 'remember_token'];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('User')
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
    /**
     * The attributes that are mass assignable.
     */


    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i A');
    }

    public function getImageAttribute($value)
    {
        if ($value != null && $value != '') {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            } else {
                return asset('uploads/user/' . $value);
            }
        }
    }
    //Relation

    public function teacherProfile()
    {
        return $this->hasOne(TeacherProfile::class, 'user_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function scopeAvailable($query)
    {
        if (auth('user')->check()) {
            $admin = auth('user')->user();
            if ($admin->hasRole('Super Admin')) {
            } elseif ($admin->hasAnyRole(['School Manager', 'Salaries Employee'])) {
                return $query->where('school_id', $admin->school_id);
            }
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new UserVerifyEmail);
    }
    //JWT

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    /**
     * Get the e-mail address where password reset links are sent.
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }
}
