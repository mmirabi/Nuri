<?php


namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\ACL\Traits\LogoutGuardTrait;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Forms\Fronts\Auth\LoginForm;
use Botble\Ecommerce\Http\Requests\LoginRequest;
use Botble\Ecommerce\Models\Customer;
use Botble\JsValidation\Facades\JsValidator;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Cryptommer\Smsir\Objects\Parameters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Cryptommer\Smsir\Smsir;

class LoginController extends BaseController
{
    public function Verify(Request $request)
    {
        $this->validateLoginPhoneCode($request);

        $mobile = $request->phone;
        $customer = Customer::where('phone', $mobile)->first();

        if ($customer) {
            $currentTime = now();
            $lastVerificationCodeSentAt = $customer->last_verification_code_sent_at;

            // اگر زمان ارسال آخرین کد تایید موجود نیست یا اختلاف زمانی بیشتر از 2 دقیقه باشد
            if (!$lastVerificationCodeSentAt || $currentTime->diffInMinutes($lastVerificationCodeSentAt) > 2) {
                // ارسال کد تایید جدید
                $name = "Code";
                $value = rand(100000, 999999);
                $parameter = new Parameters($name, $value);
                $parameters = array($parameter);
                $send = smsir::Send();
                $templateId = 100000;
                $send->Verify($mobile, $templateId, $parameters);

                // بروزرسانی زمان ارسال آخرین کد تایید
                $customer->update(['last_verification_code_sent_at' => $currentTime]);

                // اینجا لاگ اطلاعات کد تایید را اضافه کنید
                \Log::info('New verification code sent to ' . $mobile . ': ' . $value);
            } else {
                // اینجا لاگ اطلاعات کد تایید قبلی را اضافه کنید
                \Log::info('Verification code still valid for ' . $mobile . ': ' . $customer->last_verification_code);
            }
        }

        return Theme::scope(
            'ecommerce.customers.verify-sms'
        )->render();
    }

    public function showVerifyForm(Request $request)
    {
        $this->validateLoginPhone($request);


//        if ($this->attemptLogin($request)) {
//            return $this->sendLoginResponse($request);
//        }


        $mobile = $request->phone;

        // چک کردن وجود کاربر با شماره تماس
        $customer = Customer::where('phone', $mobile)->first();

        if ($customer) {
            // ارسال کد تایید
            $name = "Code";
            $value = rand(100000, 999999);
            $parameter = new Parameters($name, $value);
            $parameters = array($parameter);
            $send = smsir::Send();
            $templateId = 100000;
            $send->Verify($mobile, $templateId, $parameters);
        } else {
            // ایجاد کاربر جدید
            $customer = new Customer();
            $customer->phone = $mobile;
            $customer->name = $mobile;
            $customer->password = $mobile;
            $customer->save();
            // ارسال کد تایید
            $name = "Code";
            $value = rand(100000, 999999);
            $parameter = new Parameters($name, $value);
            $parameters = array($parameter);
            $send = smsir::Send();
            $templateId = 100000;
            $send->Verify($mobile, $templateId, $parameters);

        }

        return Theme::scope(
            'ecommerce.customers.verify-sms',
            compact('mobile')
        )->render();
    }

    protected function authenticated(Request $request, Authenticatable $user)
    {
        // اینجا هم مطمئن شوید که به درستی به مسیر مورد نظر هدایت می‌شود.
        return redirect()->intended($this->redirectPath());
    }
}
