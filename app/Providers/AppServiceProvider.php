<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('phone_unique', function ($attribute, $value, $parameters, $validator) {
            $inputs = $validator->getData();
            $code = $inputs['country_code'];
            $phone = $inputs['mobile_number'];
            $except_id = (! empty($parameters)) ? head($parameters) : null;
            $query = User::where([
                'mobile_number' => $phone,
                'country_code' => $code
            ]);
          
            if (! empty($except_id)) {
                $query->where('id', '<>', $except_id);
            }
            return !empty($query->exists())?false:true;
        });
    }
}
