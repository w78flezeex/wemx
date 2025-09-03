<?php

namespace App\Models;

use App\Events;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Punishment extends Model
{
    use HasFactory;

    protected $table = 'punishments';

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Define dispatchable events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => Events\Punishment\PunishmentCreated::class,
        'updated' => Events\Punishment\PunishmentUpdated::class,
        'deleted' => Events\Punishment\PunishmentDeleted::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unban()
    {
        $this->type = 'unbanned';
        $this->expires_at = now();
        $this->save();
    }

    public static function hasActiveBans()
    {
        $ban = Punishment::where('type', 'ipban')->where('ip_address', request()->getClientIp())->first();

        if ($ban) {
            if (isset($ban->expires_at) and $ban->expires_at->isPast()) {
                return false;
            }

            return true;
        }

        $user = auth()->user();

        if ($user) {
            $ban = Punishment::whereIn('type', ['ban', 'ipban'])->where('user_id', $user->id)->latest('created_at')->first();

            if ($ban) {
                if (isset($ban->expires_at) and $ban->expires_at->isPast()) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }

    public static function getActiveBan()
    {
        $ban = self::where('type', 'ipban')->where('ip_address', request()->getClientIp())->first();
        if ($ban) {
            if (isset($ban->expires_at) and $ban->expires_at->isPast()) {
                return false;
            }

            return $ban;
        }

        $user = auth()->user();
        if ($user) {
            $ban = Punishment::whereIn('type', ['ban', 'ipban'])->where('user_id', $user->id)->latest('created_at')->first();
            
            if ($ban) {
                if (isset($ban->expires_at) and $ban->expires_at->isPast()) {
                    return false;
                }

                return $ban;
            }
        }

        return false;
    }
}
