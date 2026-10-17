<?php

namespace App\Models;

use App\Models\Concerns\HasDisplayCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasDisplayCode, HasFactory;

    public const DISPLAY_CODE_COLUMN = 'invoice_number';

    public const DISPLAY_CODE_PREFIX = 'INV-';

    protected $fillable = [
        'customer_id',
        'customer_name',
        'po_number',
        'invoice_date',
        'due_date',
        'status',
        'enable_tax',
        'is_recurring',
        'recurring_by_month',
        'recurring_month',
        'invoice_from',
        'invoice_to',
        'taxable_amount',
        'round_off',
        'additional_charges',
        'discounts',
        'total_amount',
        'account_holder_name',
        'bank_name',
        'ifsc_code',
        'account_number',
        'terms_and_conditions',
        'notes',
        'signature_path',
        'signatory_name',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'enable_tax' => 'boolean',
            'is_recurring' => 'boolean',
            'taxable_amount' => 'decimal:2',
            'round_off' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'additional_charges' => 'array',
            'discounts' => 'array',
        ];
    }

    /** Route URLs use the invoice number, e.g. /invoice/view/INV-00001 */
    public function getRouteKeyName(): string
    {
        return 'invoice_number';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
