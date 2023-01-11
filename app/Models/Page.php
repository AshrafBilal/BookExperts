<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory;

    public static function getPages($request, $page = 10)
    {
        return self::latest()->paginate($page);
    }

    public function getPageType()
    {
        $list = [
            PAGE_TYPE_ABOUT_US => 'About Us',
            PAGE_TYPE_PRIVACY_POLICY => 'Privacy Policy',
            PAGE_TYPE_TERMS_AND_CONDITION => 'Terms And Condition'
        ];
        return isset($list[$this->type_id])?$list[$this->type_id]:'';
    }
    
}
