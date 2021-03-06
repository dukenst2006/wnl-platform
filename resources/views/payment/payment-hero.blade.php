<div>
	<section class="hero is-primary is-bold">
		<div class="hero-body has-text-centered">
			<div class="container">
				<div id="payment-steps" class="columns is-mobile">
					<div class="column is-one-third">
						@if ($step > 1) <a href="{{ route('payment-select-product') }}"> @endif
							<div class="payment-step is-active @if ($step === 1) is-current @endif">
								<span class="payment-step-count">@lang('payment.payment-steps-select-product-count')</span>
								<span class="payment-step-text is-hidden-mobile">@lang('payment.payment-steps-select-product')</span>
							</div>
						@if ($step > 1) </a> @endif
					</div>

					<div class="column is-one-third">
						@if ($step > 2) <a href="{{ route('payment-personal-data') }}"> @endif
							<div class="payment-step @if ($step > 1) is-active @endif @if ($step === 2) is-current @endif">
								<span class="payment-step-count">@lang('payment.payment-steps-personal-data-count')</span>
								<span class="payment-step-text is-hidden-mobile">@lang('payment.payment-steps-personal-data')</span>
							</div>
						@if ($step > 2) </a> @endif
					</div>

					<div class="column is-one-third">
						<div class="payment-step @if ($step > 2) is-active @endif @if ($step === 3) is-current @endif">
							<span class="payment-step-count">@lang('payment.payment-steps-confirm-order-count')</span>
							<span class="payment-step-text is-hidden-mobile">@lang('payment.payment-steps-confirm-order')</span>
						</div>
					</div>
				</div>
				<h1 class="title">
					{{ $title }}
				</h1>
				<p class="subtitle">
					{!! $subtitle  !!}
				</p>
			</div>
		</div>
	</section>
</div>
