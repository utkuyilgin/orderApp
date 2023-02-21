<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignAuthorAndCategory extends Model
{

    public $timestamps = false;

    protected $table = 'author_category_campaign';
    protected $fillable  = [
        'author_id',
        'category_id',
        'campaign_id',
    ];
    use HasFactory;
}
