<?php

namespace Remachinon\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceTunnel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'device_tunnels';

    /**
     * A tunnel belongs to a single device
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device()
    {
        return $this->belongsTo('Remachinon\Models\Device');
    }
}
