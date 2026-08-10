<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->text('memo')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('cultivation_type', 20);
            $table->text('memo')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('category', 20);
            $table->string('variety', 100)->nullable();
            $table->date('planted_on')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('memo')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('task_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('targetable');
            $table->string('name', 100);
            $table->integer('interval_days');
            $table->date('next_due_on');
            $table->boolean('auto_generate')->default(true);
            $table->text('memo')->nullable();
            $table->index(['user_id', 'targetable_type', 'targetable_id']);
            $table->timestamps();
        });

        Schema::create('work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('loggable');
            $table->dateTime('worked_at');
            $table->string('work_type', 30);
            $table->text('memo')->nullable();
            $table->string('quantity', 100)->nullable();
            $table->uuid('client_uuid')->nullable()->unique();
            $table->index(['user_id', 'loggable_type', 'loggable_id']);
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_template_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('targetable');
            $table->string('title', 200);
            $table->date('due_on');
            $table->string('status', 20)->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('work_log_id')->nullable()->constrained()->nullOnDelete();
            $table->text('memo')->nullable();
            $table->index(['user_id', 'targetable_type', 'targetable_id']);
            $table->timestamps();
        });

        Schema::create('chemicals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('category', 20);
            $table->string('resistance_code', 20)->nullable();
            $table->string('default_dilution', 50)->nullable();
            $table->text('memo')->nullable();
            $table->timestamps();
        });

        Schema::create('chemical_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chemical_id')->constrained()->restrictOnDelete();
            $table->string('dilution', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('imageable');
            $table->string('path', 255);
            $table->string('thumbnail_path', 255);
            $table->dateTime('taken_at');
            $table->text('memo')->nullable();
            $table->uuid('client_uuid')->nullable()->unique();
            $table->index(['user_id', 'imageable_type', 'imageable_id']);
            $table->timestamps();
        });

        Schema::create('hydro_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('target_ec_min', 4, 2)->nullable();
            $table->decimal('target_ec_max', 4, 2)->nullable();
            $table->decimal('target_ph_min', 4, 2)->nullable();
            $table->decimal('target_ph_max', 4, 2)->nullable();
            $table->text('composition')->nullable();
            $table->timestamps();
        });

        Schema::create('hydro_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained()->restrictOnDelete();
            $table->foreignId('hydro_recipe_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('logged_at');
            $table->string('action', 20);
            $table->decimal('ec', 4, 2)->nullable();
            $table->decimal('ph', 4, 2)->nullable();
            $table->decimal('water_temp', 4, 1)->nullable();
            $table->text('memo')->nullable();
            $table->uuid('client_uuid')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->string('token_hash', 64)->unique();
            $table->integer('expected_interval_minutes')->default(60);
            $table->timestamp('last_seen_at')->nullable();
            $table->tinyInteger('battery_percent')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('metric', 30);
            $table->decimal('value', 10, 3);
            $table->timestamp('measured_at');
            $table->index(['device_id', 'metric', 'measured_at']);
            $table->timestamps();
        });

        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('name', 100);
            $table->json('config');
            $table->timestamps();
        });

        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('metric', 30);
            $table->string('operator', 5);
            $table->decimal('threshold', 10, 3);
            $table->integer('consecutive_count')->default(1);
            $table->integer('cooldown_minutes')->default(360);
            $table->foreignId('notification_channel_id')->constrained()->restrictOnDelete();
            $table->string('label', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_rule_id')->constrained()->cascadeOnDelete();
            $table->timestamp('triggered_at');
            $table->decimal('value', 10, 3)->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->index(['alert_rule_id', 'triggered_at']);
            $table->timestamps();
        });

        Schema::create('weather_daily', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            // Stored as an Asia/Tokyo calendar date; never convert this value through UTC.
            $table->date('date');
            $table->decimal('temp_min', 4, 1)->nullable();
            $table->decimal('temp_max', 4, 1)->nullable();
            $table->decimal('precipitation_mm', 6, 1)->nullable();
            $table->tinyInteger('precipitation_probability')->nullable();
            $table->boolean('is_forecast');
            $table->json('raw')->nullable();
            $table->unique(['site_id', 'date']);
            $table->timestamps();
        });

        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20);
            $table->json('result')->nullable();
            $table->string('model', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
        Schema::dropIfExists('weather_daily');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('alert_rules');
        Schema::dropIfExists('notification_channels');
        Schema::dropIfExists('readings');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('hydro_logs');
        Schema::dropIfExists('hydro_recipes');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('chemical_applications');
        Schema::dropIfExists('chemicals');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('work_logs');
        Schema::dropIfExists('task_templates');
        Schema::dropIfExists('plants');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('sites');
    }
};
