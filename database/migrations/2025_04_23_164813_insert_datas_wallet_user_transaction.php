<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => bcrypt('password123'),
                'first_name' => 'John',
                'last_name' => 'Doe',
                'document' => '12345678901',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => bcrypt('password123'),
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'document' => '10987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('wallets')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'balance' => 1000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'balance' => 1500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('transactions')->insert([
            [
                'id' => 1,
                'wallet_id' => 1,
                'amount' => 200.00,
                'type' => 'deposit',
                'status' => 'completed',
                'sender_id' => 1,
                'receiver_id' => 1,
                'reversed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'wallet_id' => 2,
                'amount' => 300.00,
                'type' => 'transfer',
                'status' => 'pending',
                'sender_id' => 2,
                'receiver_id' => 1,
                'reversed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('transactions')->whereIn('id', [1, 2])->delete();
        DB::table('wallets')->whereIn('id', [1, 2])->delete();
        DB::table('users')->whereIn('id', [1, 2])->delete();
    }
};
