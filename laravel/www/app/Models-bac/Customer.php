<?php

//namespace App\ModelsBac;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
//
//class Customer extends Model
//{
//    use HasFactory;
//
//    protected $table = 'customer_lists';
//
//    protected $fillable = [
//        'status_id',
//        'name_ja',
//        'name_kana',
//        'birthday',
//        'introducer_id',
//        'person_in_charge_id',
//        'category_id',
//        'registered_date',
//        'tag_of_preference',
//        'notices',
//    ];
//
//    public function status()
//    {
//        return $this->belongsTo(Status::class);
//    }
//
//    public function introducer()
//    {
//        return $this->belongsTo(self::class, 'introducer_id');
//    }
//
//    public function personInCharge()
//    {
//        return $this->belongsTo(UserList::class, 'person_in_charge_id');
//    }
//
//    public function category()
//    {
//        return $this->belongsTo(CustomerCategoryList::class);
//    }
//}
