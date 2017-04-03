@extends('payment.layout')

@section('content')

	@include('payment.payment-hero', [
		'step' => 2,
		'title' => trans('payment.personal-data-title'),
		'subtitle' => trans('payment.personal-data-subtitle', [
				'name' => $product->name, 'price' => $product->price
			]),
	])

	<div class="container payment-content">
		@if (!$errors->isEmpty())
			<section class="subsection">
				<div class="notification is-warning has-text-centered">@lang('payment.personal-data-errors')</div>
			</section>
		@endif

		{!! form_start($form)  !!}

		<section class="section">
			<div class="form-header has-text-centered">
				<h2 class="title">@lang('payment.personal-data-account-heading')</h2>
				<p class="subtitle">@lang('payment.personal-data-account-lead')</p>
				<div class="notification is-info">
					<p class="strong">@lang('payment.personal-data-email-info')</p>
				</div>
			</div>
			<div class="form-group">
				<div class="control">
					{!! form_label($form->email) !!}
					{!! form_widget($form->email) !!}
					{!! form_errors($form->email) !!}
				</div>
				<div class="control">
					{!! form_label($form->password) !!}
					{!! form_widget($form->password) !!}
					{!! form_errors($form->password) !!}
				</div>
				<div class="control">
					{!! form_label($form->password_confirmation) !!}
					{!! form_widget($form->password_confirmation) !!}
					{!! form_errors($form->password_confirmation) !!}
				</div>
			</div>
		</section>
		<section id="personal-data" class="section">
			<div class="form-header has-text-centered">
				<h2 class="title">@lang('payment.personal-data-heading')</h2>
				<p class="subtitle">@lang('payment.personal-data-lead')</p>
			</div>

			<div class="form-group">
				<div class="control">
					{!! form_label($form->phone) !!}
					{!! form_widget($form->phone) !!}
					{!! form_errors($form->phone) !!}
				</div>

				<div class="control">
					{!! form_label($form->first_name) !!}
					{!! form_widget($form->first_name) !!}
					{!! form_errors($form->first_name) !!}
				</div>

				<div class="control">
					{!! form_label($form->last_name) !!}
					{!! form_widget($form->last_name) !!}
					{!! form_errors($form->last_name) !!}
				</div>

				<div class="control">
					{!! form_label($form->address) !!}
					{!! form_widget($form->address) !!}
					{!! form_errors($form->address) !!}
				</div>

				<div class="control">
					{!! form_label($form->zip) !!}
					{!! form_widget($form->zip) !!}
					{!! form_errors($form->zip) !!}
				</div>

				<div class="control">
					{!! form_label($form->city) !!}
					{!! form_widget($form->city) !!}
					{!! form_errors($form->city) !!}
				</div>
			</div>

			<div class="box">
				<div id="personal-data-invoice-toggle">
					{!! form_widget($form->invoice) !!}
					{!! form_label($form->invoice) !!}
					{!! form_errors($form->invoice) !!}
				</div>
				<div id="personal-data-invoice-form" class="form-group @if (Session::get('_old_input.invoice')) show @else hidden @endif">
					<div class="control">
						{!! form_label($form->invoice_name) !!}
						{!! form_widget($form->invoice_name) !!}
						{!! form_errors($form->invoice_name) !!}
					</div>
					<div class="control">
						{!! form_label($form->invoice_nip) !!}
						{!! form_widget($form->invoice_nip) !!}
						{!! form_errors($form->invoice_nip) !!}
					</div>
					<div class="control">
						{!! form_label($form->invoice_address) !!}
						{!! form_widget($form->invoice_address) !!}
						{!! form_errors($form->invoice_address) !!}
					</div>
					<div class="control">
						{!! form_label($form->invoice_zip) !!}
						{!! form_widget($form->invoice_zip) !!}
						{!! form_errors($form->invoice_zip) !!}
					</div>
					<div class="control">
						{!! form_label($form->invoice_city) !!}
						{!! form_widget($form->invoice_city) !!}
						{!! form_errors($form->invoice_city) !!}
					</div>
					<div class="control">
						{!! form_label($form->invoice_country) !!}
						{!! form_widget($form->invoice_country) !!}
						{!! form_errors($form->invoice_country) !!}
					</div>
				</div>
			</div>
		</section>

		<section class="section">
			<div class="form-header has-text-centered">
				<h2 class="title">@lang('payment.personal-data-consents-heading')</h2>
				<p class="subtitle">@lang('payment.personal-data-consents-lead')</p>
			</div>

			<div class="form-group small">
				<div class="box">
					<div class="control">
						{!! form_widget($form->consent_account) !!}
						{!! html_entity_decode(form_label($form->consent_account)) !!}
						{!! form_errors($form->consent_account) !!}
					</div>
					<div class="control">
						{!! form_widget($form->consent_order) !!}
						{!! html_entity_decode(form_label($form->consent_order)) !!}
						{!! form_errors($form->consent_order) !!}
					</div>
				</div>
			</div>

			<div class="form-group small">
				<p class="form-header">@lang('payment.personal-data-consent-newsletter-heading')</p>
				<div class="box">
					<div class="control">
						{!! form_widget($form->consent_newsletter) !!}
						{!! html_entity_decode(form_label($form->consent_newsletter)) !!}
						{!! form_errors($form->consent_newsletter) !!}
					</div>
				</div>
			</div>

			<div class="tou form-group small">
				<p class="form-header">@lang('payment.personal-data-tou-heading')</p>
				<div class="box">
					<div class="control">
						{!! form_widget($form->consent_terms) !!}
						{!! html_entity_decode(form_label($form->consent_terms)) !!}
						{!! form_errors($form->consent_terms) !!}
					</div>
				</div>
			</div>
		</section>

		<section class="form-end">
			<div class="block has-text-centered">
				<button class="button is-primary">@lang('payment.personal-data-submit')</button>
			</div>
		</section>

		{!! form_end($form, false)  !!}

	</div>

@endsection
