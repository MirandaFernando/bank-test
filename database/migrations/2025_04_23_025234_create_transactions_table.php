<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['deposit', 'transfer']);
            $table->enum('status', ['completed', 'reversed', 'pending'])->default('pending');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('reversed_at')->nullable();
            $table->timestamps();
            
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade'); // Adicionei relação com wallet
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
