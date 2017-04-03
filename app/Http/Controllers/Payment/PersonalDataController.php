<?php

namespace App\Http\Controllers\Payment;

use App\Http\Forms\SignUpForm;
use App\Mail\SignUpConfirmation;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PersonalDataController extends Controller
{
	use FormBuilderTrait;

	public function index(FormBuilder $formBuilder, $productSlug = null)
	{
		if ($productSlug !== null) {
			$product = Product::slug($productSlug);

			if ($product instanceof Product) {
				Session::put('product', $product);
			}
		}

		$product = Session::get('product');
		if (!$product instanceof Product || !$product->available) {
			return redirect()->route('payment-select-product');
		}

		$form = $this->form(SignUpForm::class, [
			'method' => 'POST',
			'url'    => route('payment-personal-data-post'),
			'model'  => Auth::user(),
		])->modify('password', 'password', [
			'value' => '',
		]);

		return view('payment.personal-data', [
			'form'    => $form,
			'product' => $product,
		]);

	}

	public function handle(Request $request)
	{
		$form = $this->form(SignUpForm::class);

		if (Auth::check()) {
			$user = Auth::user();
			// Authenticated users should be able to edit only their own account.
			$form->validate(['email' => 'required|email|in:' . $user->email]);
		}

		if (!$form->isValid()) {
			Log::notice('Sing up form invalid, redirecting...');
			return redirect()->back()->withErrors($form->getErrors())->withInput();
		}

		Log::notice('Creating user account');
		$user = User::updateOrCreate(
			['email' => $request->get('email')],
			[
				'first_name'         => $request->get('first_name'),
				'last_name'          => $request->get('last_name'),
				'address'            => $request->get('address'),
				'zip'                => $request->get('zip'),
				'city'               => $request->get('city'),
				'email'              => $request->get('email'),
				'phone'              => $request->get('phone'),
				'password'           => bcrypt($request->get('password')),
				'invoice'            => $request->get('invoice') ?? 0,
				'invoice_name'       => $request->get('invoice_name'),
				'invoice_nip'        => $request->get('invoice_nip'),
				'invoice_address'    => $request->get('invoice_address'),
				'invoice_zip'        => $request->get('invoice_zip'),
				'invoice_city'       => $request->get('invoice_city'),
				'invoice_country'    => $request->get('invoice_country'),
				'consent_newsletter' => $request->get('consent_newsletter') ?? 0,
				'consent_account'    => $request->get('consent_account') ?? 0,
				'consent_order'      => $request->get('consent_order') ?? 0,
				'consent_terms'      => $request->get('consent_terms') ?? 0,
			]
		);
		Log::notice('Creating order');
		$order = $user->orders()->create([
			'product_id' => Session::get('product')->id,
			'session_id' => str_random(32),
		]);

		if ($user->is_subscriber){
			$order->attachCoupon(Coupon::slug('subscriber-coupon'));
		}

		Auth::login($user);
		Log::notice('User automatically logged in after registration.');

		return redirect(route('payment-confirm-order'));
	}

	/**
	 * @param $productSlug
	 * @return null|Product
	 */
	private function getProduct($productSlug = null)
	{
		$product = Session::get('product', function () use ($productSlug) {
			return Product::slug($productSlug);
		});

		if ($product instanceof Product && $product !== null && $product->slug !== $productSlug) {
			return $product;
		}

		return Product::slug($productSlug);
	}
}
