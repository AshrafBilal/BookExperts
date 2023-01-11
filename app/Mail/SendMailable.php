<?php
namespace App\Mail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Http\Controllers\Controller;

class EmailController extends Controller
{
    public function sendEmail()
    {
        Mail::to('mail@appdividend.com')->send(new SendMail());
        echo 'email sent';
    }
}

?>