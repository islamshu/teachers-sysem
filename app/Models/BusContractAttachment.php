<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BusContractAttachment extends Pivot
{
    protected $table = 'bus_contract_attachment';

    protected $fillable = [
        'bus_contract_id',
        'contract_attachment_id',
        'file_path',
    ];
}
