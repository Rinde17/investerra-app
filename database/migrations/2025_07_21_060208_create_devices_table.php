<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Lien vers l'utilisateur
            $table->string('uuid')->unique()->nullable(); // Identifiant unique de l'appareil (généré côté client ou serveur)
            $table->string('user_agent')->nullable(); // Ex: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36..."
            $table->string('ip_address', 45)->nullable(); // Adresse IP de connexion (45 pour supporter IPv6)
            $table->string('name')->nullable(); // Nom donné par l'utilisateur (ex: "Mon PC de bureau")
            $table->timestamp('last_used_at')->nullable(); // Dernière utilisation de l'appareil
            $table->boolean('is_trusted')->default(false); // Si l'appareil est marqué comme fiable
            $table->timestamps(); // created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
