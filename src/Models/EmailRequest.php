<?php

namespace Larangular\EmailRecord\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Larangular\EmailRecord\Models\SentEmail;
use Larangular\RoutingController\Model as RoutingModel;

class EmailRequest extends Model {

    use SoftDeletes, RoutingModel;

    protected $table    = 'email_requests';
    protected $dates    = ['deleted_at'];
    protected $fillable = [
        'content_id',
        'email_type',
        'sent_email_id',
    ];
    protected $with     = ['sentEmail'];
    protected $appends  = ['emailTypeClass'];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->connection = config('email-record.connection');
    }

    public function sentEmail() {
        return $this->hasOne(SentEmail::class, 'id', 'sent_email_id')
                    ->withTrashed();
    }

    public function getEmailTypeClassAttribute() {
        $type = $this->attributes['email_type'];
        return config('email-record.email_types.' . $type);
    }

    public function scopeNotSent($query) {
        return $query->where('sent_email_id', null);
    }

}
