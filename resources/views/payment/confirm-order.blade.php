@extends('layouts.payment')

@section('content')
	<div class="row">
		<div class="col-xs-12">
			<h2 class="text-center">@lang('payment.confirm-heading')</h2>

			<div class="panel panel-default">
				<div class="panel-heading">@lang('payment.confirm-order-heading')</div>
				<div class="panel-body">
					<p class="lead">{{ $order->product->name }}</p>
					<p>@lang('payment.confirm-order-price', [ 'price' => $order->product->price ])</p>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">@lang('payment.confirm-personal-data-heading')</div>
				<div class="panel-body">
					<p class="lead">{{ $user->email }}</p>
					<ul class="list-unstyled">
						<li><strong>{{ $user->full_name }}</strong></li>
						<li>{{ $user->address }}</li>
						<li>{{ $user->zip }}, {{ $user->city }}</li>
					</ul>
				</div>
			</div>

			@if($user->invoice)
				<div class="panel panel-default">
					<div class="panel-heading">@lang('payment.personal-data-invoice-heading')</div>
					<div class="panel-body">
						<ul class="list-unstyled">
							<li><strong>{{ $user->invoice_name }}</strong></li>
							<li>{{ $user->invoice_address }}</li>
							<li>{{ $user->invoice_zip }}, {{ $user->invoice_city }}</li>
							<li>{{ $user->invoice_country }}</li>
						</ul>
					</div>
				</div>
			@endif

			<div>
				<p class="text-center">
					<a href="{{ route('payment-personal-data') }}">@lang('payment.confirm-change-order')</a>
				</p>
			</div>
		</div>
	</div>

	<hr>

	<div class="row text-center">
		<div class="col-xs-12">
			<h2>@lang('payment.confirm-method-heading')</h2>
			<p class="lead">@lang('payment.confirm-method-lead')</p>
		</div>
		<div class="col-xs-12 col-sm-6">
			<form action="{{route('payment-confirm-order-post')}}" method="post">
				{!! csrf_field() !!}
				<input type="hidden" name="method" value="transfer"/>
				<button class="btn btn-default">@lang('payment.confirm-method-bank-transfer-button')</button>
			</form>
		</div>
		<div class="col-xs-12 col-sm-6">
			<form action="{{ config('przelewy24.transaction_url') }}" method="post" class="p24_form">

				<input type="hidden" name="p24_session_id" value="{{ $order->session_id }}"/>
				<input type="hidden" name="p24_merchant_id" value="{{ config('przelewy24.merchant_id') }}"/>
				<input type="hidden" name="p24_pos_id" value="{{ config('przelewy24.merchant_id') }}"/>
				<input type="hidden" name="p24_amount" value="{{ (int)$order->product->price * 100 }}"/>
				<input type="hidden" name="p24_currency" value="PLN"/>
				<input type="hidden" name="p24_description" value="{{ $order->product->name }}"/>
				<input type="hidden" name="p24_client" value="{{ $user->full_name }}"/>
				<input type="hidden" name="p24_address" value="{{ $user->address }}"/>
				<input type="hidden" name="p24_zip" value="{{ $user->zip }}"/>
				<input type="hidden" name="p24_city" value="{{ $user->city }}"/>
				<input type="hidden" name="p24_country" value="PL"/>
				<input type="hidden" name="p24_email" value="{{ $user->email }}"/>
				<input type="hidden" name="p24_language" value="pl"/>
				<input type="hidden" name="p24_url_return" value="{{ route('profile-orders') }}"/>
				<input type="hidden" name="p24_url_status" value="{{ route('payment-status-hook')  }} "/>
				<input type="hidden" name="p24_api_version" value="{{config('przelewy24.api_version')}}"/>
				<input type="hidden" name="p24_sign" value="{{ $checksum }}"/>

			</form>

			<button class="btn btn-primary p24_submit">@lang('payment.confirm-method-online-payment-button')</button>
		</div>
	</div>

@endsection