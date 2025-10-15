<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpireGuests extends Command
{
    protected $signature = 'guests:expire';
    protected $description = 'Clear data for guest users whose guest_expired_at has passed (keep empty account row)';

    public function handle()
    {
        $now = now();
        $users = User::whereNotNull('guest_expired_at')->where('guest_expired_at', '<=', $now)->get();
        foreach ($users as $user) {
            // remove personal data but preserve the user row for audit
            $user->name = null;
            $user->email = null;
            $user->password = null;
            $user->syncRoles([]);
            $user->guest_expired_at = null;
            $user->save();
            $this->info('Cleared guest: '.$user->id);
        }

        $this->info('Guest expiration run completed.');
        return 0;
    }
}
