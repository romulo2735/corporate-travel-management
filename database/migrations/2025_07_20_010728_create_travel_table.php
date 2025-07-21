<?php

use App\TravelStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel', function (Blueprint $table) {
            $table->id();
            $table->string('requester_name');
            $table->string('destination');
            $table->date('departure_date');
            $table->date('return_date');
            $table->enum('status', array_column(TravelStatusEnum::cases(), 'value'))->default(TravelStatusEnum::REQUESTED->value);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel');
    }
};
