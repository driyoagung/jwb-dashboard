<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreatePosAdmin extends Command
{
    protected $signature = 'pos:create-admin';

    protected $description = 'Create an active POS administrator account';

    public function handle(): int
    {
        $data = [
            'name' => $this->ask('Nama'),
            'email' => $this->ask('Email'),
            'password' => $this->secret('Kata sandi (minimal 12 karakter)'),
        ];
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create($validator->validated() + ['role' => 'admin', 'is_active' => true]);
        $this->info('Admin POS berhasil dibuat.');

        return self::SUCCESS;
    }
}
