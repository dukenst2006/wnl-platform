<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ListOrders extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'orders {id?*}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List all orders';

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$orderId = $this->argument('id');

		if (empty ($orderId)) {
			$orders = Order::with(['user', 'product'])->get();
		} else {
			$orders = Order::with(['user', 'product'])->whereIn('id', $orderId)->get();

			if (!$orders) {
				$this->error("Sorry, it seems that there is no order with ID {$orderId} in the database.");
				exit;
			}
		}

		$orders = $orders->map(function ($order) {
			return [
				$order->id,
				$order->user_id,
				$order->user->email,
				$order->user->full_name,
				$order->product->name,
				$order->paid,
				$order->paid_amount,
				$order->method,
				$order->external_id,
				$order->canceled,
				$order->created_at,
			];
		})->toArray();

		$this->table(
			[
				'order id',
				'user ID',
				'user email',
				'user name',
				'product',
				'paid',
				'paid_amount',
				'method',
				'p24 ID',
				'canceled',
				'created_at',
			],
			$orders
		);

		return true;
	}
}
