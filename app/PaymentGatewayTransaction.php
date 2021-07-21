<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayTransaction extends Model
{
    protected $fillable = [
        'gateway_id',
        'customer_id',
        'gateway_identifier',
        'amount',
        'checksum',
        'order_id',
        'payload',
        'status',
        'payment_link',
        'status',
        'transaction_id',
        'callback_url',
    ];

    public function toArray()
    {
        $array = parent::toArray();
        foreach ($this->getMutatedAttributes() as $key) {
            if (!array_key_exists($key, $array)) {
                $array[$key] = $this->{$key};
            }
        }
        return $array;
    }
}
