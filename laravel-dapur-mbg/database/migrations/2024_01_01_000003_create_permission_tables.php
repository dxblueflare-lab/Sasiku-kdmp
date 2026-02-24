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
        $config = config('permission.table_names');
        $foreignKeys = config('permission.column_names');

        if (empty($config['pivot_role_user'])) {
            $config['pivot_role_user'] = $config['table_names']['role_user'];
        }
        if (empty($config['pivot_permission_user'])) {
            $config['pivot_permission_user'] = $config['table_names']['permission_user'];
        }
        if (empty($config['pivot_permission_role'])) {
            $config['pivot_permission_role'] = $config['table_names']['permission_role'];
        }

        Schema::create($config['roles'], function (Blueprint $table) {
            $table->bigIncrements('id'); // role id
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($config['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id'); // permission id
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($config['model_has_permissions'], function (Blueprint $table) use ($config, $foreignKeys) {
            $table->unsignedBigInteger('permission_id');

            $table->string('model_type');
            $table->unsignedBigInteger($foreignKeys['model_morph_key']);
            $table->index([$foreignKeys['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign('permission_id')
                ->references('id') // permission id
                ->on($config['permissions'])
                ->onDelete('cascade');

            $table->primary(['permission_id', $foreignKeys['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
        });

        Schema::create($config['model_has_roles'], function (Blueprint $table) use ($config, $foreignKeys) {
            $table->unsignedBigInteger('role_id');

            $table->string('model_type');
            $table->unsignedBigInteger($foreignKeys['model_morph_key']);
            $table->index([$foreignKeys['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign('role_id')
                ->references('id') // role id
                ->on($config['roles'])
                ->onDelete('cascade');

            $table->primary(['role_id', $foreignKeys['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
        });

        Schema::create($config['role_has_permissions'], function (Blueprint $table) use ($config) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id') // permission id
                ->on($config['permissions'])
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id') // role id
                ->on($config['roles'])
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $config = config('permission.table_names');

        if (empty($config['pivot_role_user'])) {
            $config['pivot_role_user'] = $config['table_names']['role_user'];
        }
        if (empty($config['pivot_permission_user'])) {
            $config['pivot_permission_user'] = $config['table_names']['permission_user'];
        }
        if (empty($config['pivot_permission_role'])) {
            $config['pivot_permission_role'] = $config['table_names']['permission_role'];
        }

        Schema::drop($config['role_has_permissions']);
        Schema::drop($config['model_has_roles']);
        Schema::drop($config['model_has_permissions']);
        Schema::drop($config['roles']);
        Schema::drop($config['permissions']);
    }
};