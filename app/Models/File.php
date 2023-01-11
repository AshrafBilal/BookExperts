<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    const TYPE_IMAGE = 0;

    const TYPE_VIDEO = 1;

    const TYPE_PDF = 2;

    const TYPE_OTHER = 3;

    public function jsonResponse()
    {
        $json = [];

        $json['id'] = $this->id;
        $json['file'] = $this->file;
        $json['original_name'] = $this->original_name;
        $json['created_at'] = $this->created_at->toDateTimeString();
        $json['updated_at'] = $this->updated_at->toDateTimeString();
        return $json;
    }

    public function delete()
    {
        $this->unlinkFiles();
        return parent::delete();
    }

    public function unlinkFiles()
    {
        $file = basename($this->file);
        if (! empty($file)) {
            @Storage::disk('images')->delete($file);
        }
        return true;
    }
}
