<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'user_id',
        'photo_product',
        'category',
        'shipping_method',
        'map',
        'availability', 
    ];

    //define the relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoritedBy()
    {
    return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
