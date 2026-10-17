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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->nullable()->unique(); // display code, e.g. INV-00001
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name'); // kept so the invoice still shows a name if the user is deleted
            $table->string('po_number', 50);
            $table->date('invoice_date')->nullable();
            $table->date('due_date');
            $table->string('status', 20)->default('draft')->index();
            $table->boolean('enable_tax')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_by_month', 20)->nullable();
            $table->string('recurring_month', 50)->nullable();
            $table->text('invoice_from')->nullable();
            $table->text('invoice_to')->nullable();

            // Totals
            $table->decimal('taxable_amount', 12, 2)->default(0);
            $table->decimal('round_off', 12, 2)->default(0);
            $table->json('additional_charges')->nullable(); // list of amounts
            $table->json('discounts')->nullable();          // list of percentages
            $table->decimal('total_amount', 12, 2)->default(0);

            // Payment details
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ifsc_code', 20)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable();

            // Signature
            $table->string('signature_path')->nullable();
            $table->string('signatory_name');

            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('item');
            $table->string('category');
            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->decimal('discount', 5, 2)->default(0); // percentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
