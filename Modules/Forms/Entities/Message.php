<?php

namespace Modules\Forms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;

class Message extends Model
{
    protected $table = 'module_forms_submissions_messages';

    protected $fillable = [
        'submission_id',
        'user_id',
        'guest_email',
        'ip_address',
        'user_agent',
        'message',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function email()
    {
        return $this->guest_email ?? $this->user->email;
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function notifyNewMessage()
    {
        if(auth()->user() AND auth()->user()->isAdmin()) {
            if($this->submission->form->can_view_submission) {
                $button = [
                    'name' => 'View Message',
                    'url' => route('forms.view-submission', $this->submission->token),
                ];
            } else {
                $button = NULL;
            }

            $this->submission->emailUser([
                'subject' => 'You have a new message',
                'content' => "Your submission has a new message <br> <br> <code>{$this->message}</code>",
                'button' => $button,
            ]);
        } else {
            $this->submission->emailAdmin([
                'subject' => 'New message on submission',
                'content' => "A submission has a new message <br> <br> <code>{$this->message}</code>",
                'button' => [
                    'name' => 'View Submission',
                    'url' => route('forms.view-submission', $this->submission->token),
                ],
            ]);
        }
    }
}

