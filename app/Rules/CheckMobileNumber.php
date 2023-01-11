<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class CheckMobileNumber implements Rule
{

    public $country_code;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($country_code)
    {
        $this->country_code = $country_code;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $account = User::where([
            'mobile_number' => $value,
            'country_code' => $this->country_code
        ])->exists();
        return ! empty($account) ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Mobile number already has been taken.';
    }
}
