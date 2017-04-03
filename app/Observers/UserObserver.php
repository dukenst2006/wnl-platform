<?php

namespace App\Observers;


use App\Mail\SignUpConfirmation;
use App\Models\Order;
use App\Jobs\OrderPaid;
use App\Jobs\IssueInvoice;
use App\Jobs\OrderConfirmed;
use App\Models\User;
use Illuminate\Support\Facades\App;
use App\Notifications\OrderCreated;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Mail;


class UserObserver
{
	use DispatchesJobs, Notifiable;

	public function created(User $user)
	{
		Mail::to($user)->send(new SignUpConfirmation($user));
	}
}
