<?php

namespace Larangular\EmailRecord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larangular\Installable\Facades\InstallableConfig;
use Larangular\RoutingController\Model as RoutingModel;

class EmailRequest extends Model {

    use SoftDeletes, RoutingModel;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'content_id',
        'email_type',
        'sent_email_id',
    ];
    protected $with = ['sentEmail'];
    protected $appends = [
        'email_type_class',
        'email_type_name',
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $installableConfig = InstallableConfig::config('Larangular\EmailRecord\EmailRecordServiceProvider');
        $this->connection = $installableConfig->getConnection('email_requests');
        $this->table = $installableConfig->getName('email_requests');
    }

    public function sentEmail() {
        return $this->hasOne(SentEmail::class, 'id', 'sent_email_id')
                    ->withTrashed();
    }

    public function getEmailTypeClassAttribute() {
        return config('email-record.email_types.' . $this->attributes['email_type'] . '.type_class');
    }

    public function getEmailTypeNameAttribute() {
        return config('email-record.email_types.' . $this->attributes['email_type'] . '.type_name');
    }

    public function scopeNotSent($query) {
        return $query->where('sent_email_id', null);
    }

}
