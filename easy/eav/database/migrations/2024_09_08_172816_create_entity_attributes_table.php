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
        // Entity Types Table
        Schema::create('entity_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('entity_table');
            $table->timestamps();
        });

        // Attribute Sets Table
        Schema::create('attribute_sets', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('label');
            $table->foreignId('entity_type_id')->constrained('entity_types')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['code', 'entity_type_id'], 'set_code_entity_id_unique');
        });

        // Attribute Groups Table
        Schema::create('attribute_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('label');
            $table->integer('sort_order');
            $table->foreignId('entity_type_id')->constrained('entity_types')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['code', 'entity_type_id'], 'group_code_entity_id_unique');
        });

        // Attributes Table
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('label');
            $table->string('storage');
            $table->string('input');
            $table->boolean('is_static')->default(false);
            $table->boolean('is_unique')->default(false);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->mediumText('default_value')->nullable();
            $table->foreignId('entity_type_id')->constrained('entity_types')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['code', 'entity_type_id'], 'attributes_entity_type_id_unique');
        });

        // Pivot table for Attribute Sets and Attribute Groups
        Schema::create('attribute_set_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_set_id')->constrained('attribute_sets')->onDelete('cascade');
            $table->foreignId('attribute_group_id')->constrained('attribute_groups')->onDelete('cascade');
            $table->timestamps();
        });

        // Pivot table for Attributes, Attribute Sets, and Attribute Groups
        Schema::create('attribute_group_set_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade');
            $table->foreignId('attribute_group_id')->constrained('attribute_groups')->onDelete('cascade');
            $table->foreignId('attribute_set_id')->constrained('attribute_sets')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint to ensure an attribute doesn't belong to different groups within the same set
            $table->unique(['attribute_id', 'attribute_group_id', 'attribute_set_id'], 'attr_group_set_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_group_set_attribute');
        Schema::dropIfExists('attribute_set_group');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('attribute_groups');
        Schema::dropIfExists('attribute_sets');
        Schema::dropIfExists('entity_types');
    }
};
