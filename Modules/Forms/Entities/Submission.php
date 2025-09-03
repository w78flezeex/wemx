<?php

namespace Modules\Forms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\EmailHistory;

class Submission extends Model
{
    protected $table = 'module_forms_submissions';

    protected $fillable = [
        'form_id',
        'user_id',
        'token',
        'guest_email',
        'status',
        'data',
        'ip_address',
        'user_agent',
        'paid',
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Set global scope to tenant
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->token = Str::uuid();
        });
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function email()
    {
        return $this->guest_email ?? $this->user->email;
    }

    public function onPaid()
    {
        $this->paid = true;
        $this->status = 'open';
        $this->save();
    }

    public function notifyNewSubmission()
    {
        // email the admin
        $this->emailAdmin([
            'subject' => 'New Submission: ' . $this->form->title,
            'content' => 'You have received a new submission for the form ' . $this->form->title . '.',
            'button' => [
                'name' => 'View Submission',
                'url' => route('forms.view-submission', $this->token),
            ],
        ]);

        // email the user
        if($this->form->can_view_submission) {
            $button = [
                'name' => 'View Submission',
                'url' => route('forms.view-submission', $this->token),
            ];
        } else {
            $button = NULL;
        }

        $this->emailUser([
            'subject' => 'Your submission has been received.',
            'content' => 'Your submission for the form ' . $this->form->title . ' has been received. Our team will review your submission and get back to you soon.',
            'button' => $button,
        ]);
    }

    public function emailAdmin($email)
    {
        if($this->form->notification_email) { 
            $user = User::where('email', $this->form->notification_email)->first();
            if($user) {
                $user->email([
                    'subject' => $email['subject'],
                    'content' => $email['content'],
                    'button' => $email['button'] ?? NULL,
                ]);
            } else {
                // email the contact submission to administrator
                $email = EmailHistory::query()->create([
                    'user_id' => null,
                    'sender' => config('mail.from.address'),
                    'receiver' => $this->form->notification_email,
                    'subject' => $email['subject'],
                    'content' => $email['content'],
                    'button' => $email['button'] ?? NULL,
                    'attachment' => NULL,
                ]);
            }
        }
    }

    public function emailUser($email) 
    {
        if($this->user) {
            $this->user->email([
                'subject' => $email['subject'],
                'content' => $email['content'],
                'button' => $email['button'] ?? NULL,
            ]);
        } else {
            if(!$this->guest_email) {
                return;
            }

            // email the contact submission to user
            $email = EmailHistory::query()->create([
                'user_id' => null,
                'sender' => config('mail.from.address'),
                'receiver' => $this->email(),
                'subject' => $email['subject'],
                'content' => $email['content'],
                'button' => $email['button'] ?? NULL,
                'attachment' => NULL,
            ]);
        }
    }
}

