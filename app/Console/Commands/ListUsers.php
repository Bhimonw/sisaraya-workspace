<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users with their roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::with('roles')->get();
        
        $this->info('📋 Sisaraya Members (' . $users->count() . ' users)');
        $this->newLine();
        
        $tableData = [];
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->join(', ') ?: '-';
            $tableData[] = [
                $user->id,
                $user->name,
                '@' . $user->username,
                $user->email,
                $roles
            ];
        }
        
        $this->table(
            ['ID', 'Name', 'Username', 'Email', 'Roles'],
            $tableData
        );
        
        return 0;
    }
}
