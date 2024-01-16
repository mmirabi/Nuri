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
    use AuthenticatesUsers, LogoutGuardTrait {
        AuthenticatesUsers::attemptLogin as baseAttemptLogin;
    }

    public string $redirectTo = '/';

    public function Verify()
    {

        return Theme::scope(
            'ecommerce.customers.verify-sms'
        )->render();
    }
    public function showVerifyForm(Request $request)
    {
        $this->validateLoginPhone($request);

        // ...

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // ...

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
            $customer->email = "default@example.com";
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
            'ecommerce.customers.verify-sms'
        )->render();
    }
    public function __construct()
    {
        $this->middleware('customer.guest', ['except' => 'logout']);
    }

    public function showLoginForm()
    {
        SeoHelper::setTitle(__('Login'));

        Theme::breadcrumb()->add(__('Login'), route('customer.login'));

        if (! session()->has('url.intended') &&
            ! in_array(url()->previous(), [route('customer.login')])
        ) {
            session(['url.intended' => url()->previous()]);
        }

        Theme::asset()
            ->container('footer')
            ->usePath(false)
            ->add('js-validation', 'vendor/core/core/js-validation/js/js-validation.js', ['jquery']);

        add_filter(THEME_FRONT_FOOTER, function ($html) {
            return $html . JsValidator::formRequest(LoginRequest::class)->render();
        });

        return Theme::scope(
            'ecommerce.customers.login',
            ['form' => LoginForm::create()],
            'plugins/ecommerce::themes.customers.login'
        )->render();
    }

    protected function guard()
    {
        return auth('customer');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, (new LoginRequest())->rules());
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to log in and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        $this->sendFailedLoginResponse();
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $this->loggedOut($request);

        return redirect()->to(route('public.index'));
    }

    protected function attemptLogin(Request $request)
    {
        if ($this->guard()->validate($this->credentials($request))) {
            $customer = $this->guard()->getLastAttempted();

            if (EcommerceHelper::isEnableEmailVerification() && empty($customer->confirmed_at)) {
                throw ValidationException::withMessages([
                    'confirmation' => [
                        __(
                            'The given email address has not been confirmed. <a href=":resend_link">Resend confirmation link.</a>',
                            [
                                'resend_link' => route('customer.resend_confirmation', ['email' => $customer->email]),
                            ]
                        ),
                    ],
                ]);
            }

            if ($customer->status->getValue() !== CustomerStatusEnum::ACTIVATED) {
                throw ValidationException::withMessages([
                    'email' => [
                        __('Your account has been locked, please contact the administrator.'),
                    ],
                ]);
            }

            return $this->baseAttemptLogin($request);
        }

        return false;
    }

    public function phone(): string
    {
        return EcommerceHelper::isLoginUsingPhone() ? 'username' : 'phone';
    }
}
