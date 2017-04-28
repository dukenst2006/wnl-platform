<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Console\Command;

class OptimaExport extends Command
{
	const DATABASE_ID = 'abc';
	const ADVANCE_SERIES_NAME = 'F-ZAL';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'invoice:export';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
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
		$contractors = [];
		$invoices = [];

		$orders = Order::with(['invoices', 'user', 'product'])
			->whereHas('invoices', function ($query) {
				$query->where('series', self::ADVANCE_SERIES_NAME);
			})->get();

		foreach ($orders as $order) {
			list($contractors[], $invoices[]) = $this->processOrder($order);
		}

		$data = [
			'KONTRAHENCI'            => [
				'WERSJA'      => '2.00',
				'BAZA_ZRD_ID' => self::DATABASE_ID,
				'BAZA_DOC_ID' => self::DATABASE_ID,
				'KONTRACHENT' => $contractors,
			],
			'WALUTY'                 => [
				'WERSJA'      => '2.00',
				'BAZA_ZRD_ID' => self::DATABASE_ID,
				'BAZA_DOC_ID' => self::DATABASE_ID,
			],
			'REJESTRY_SPRZEDAZY_VAT' => [
				'WERSJA'                => '2.00',
				'BAZA_ZRD_ID'           => self::DATABASE_ID,
				'BAZA_DOC_ID'           => self::DATABASE_ID,
				'REJESTR_SPRZEDAZY_VAT' => $invoices,
			],
		];

//		dd($this->toXml($data));
		\Storage::put('exports/optima.xml', $this->toXml($data));
	}

	public function processXmlNode($data, $xmlNode)
	{
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				if (!$this->isAssoc($value) && !empty($value)) {
					foreach ($value as $item) {
						$subNode = $xmlNode->addChild($key);
						$this->processXmlNode($item, $subNode);
					}
				} else {
					$subNode = $xmlNode->addChild($key);
					$this->processXmlNode($value, $subNode);
				}
			} else {
				$xmlNode->addChild($key, $value);
			}
		}

		return $xmlNode;
	}

	protected function toXml($data)
	{
		$xmlData = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>' .
			'<ROOT xmlns="http://www.comarch.pl/cdn/optima/offline"></ROOT>');

		$simpleXml = $this->processXmlNode($data, $xmlData);

		// A weird hack to get the XML formatted,
		// I haven't found any other way to do that.
		$dom = dom_import_simplexml($simpleXml)->ownerDocument;
		$dom->formatOutput = true;
		$xml = $dom->saveXML();

		return $xml;
	}

	protected function isAssoc(array $array)
	{
		if (array() === $array) return false;

		return array_keys($array) !== range(0, count($array) - 1);
	}

	protected function processOrder($order)
	{
		$user = $order->user;
		$invoice = $order->invoices->last();
		$product = $order->product;

		if ($user->invoice) {
			$name = $user->invoice_name;
			$country = $user->invoice_country;
			$street = $user->invoice_address;
			$zip = $user->invoice_zip;
			$city = $user->invoice_city;
			$vatId = $user->invoice_nip;
		} else {
			$name = $user->full_name;
			$country = 'Polska';
			$street = $user->address;
			$zip = $user->zip;
			$city = $user->city;
			$vatId = '';
		}

		$contractor = [
			'ID_ZRODLA'  => [],
			'AKRONIM'    => $user->id,
			'OPIS'       => '',
			'CHRONIONY'  => 'Nie',
			'RODZAJ'     => '',
			'MAX_ZWLOKA' => '',
			'UPUST'      => '',
			'ADRESY'     => [
				'ADRES' => [
					'STATUS'       => 'aktualny',
					'NAZWA1'       => $name,
					'KRAJ'         => $country,
					'ULICA'        => $street,
					'KOD_POCZTOWY' => $zip,
					'MIASTO'       => $city,
					'NIP'          => $vatId,
					'REGON'        => '',
					'TELEFON'      => '',
					'FAX'          => '',
					'URL'          => '',
					'EMAIL'        => '',
				],
			],
		];

		$date = $invoice->created_at->format('Y-m-d');
		$deadline = $invoice->created_at->addWeek()->format('Y-m-d');

		if ($invoice->vat === 'zw') {
			$vatRate = '0';
			$vatStatus = 'zwolniona';
			$uwz = 'warunkowo';
		} else {
			$vatRate = '23';
			$vatStatus = 'opodatkowana';
			$uwz = 'Tak';
		}

		$priceNet = $this->price($invoice->amount / (1 + (int)$vatRate));
		$vatValue = $this->price($invoice->amount - $invoice->amount / (1 + (int)$vatRate));
		$priceGross = $this->price($invoice->amount);

		$invoice = [
			'ID_ZRODLA'               => '',
			'MODUL'                   => 'Rejestr Vat',
			'TYP'                     => 'Rejestr sprzedaży',
			'REJESTR'                 => 'SPRZEDAŻ',
			'DATA_WYSTAWIENIA'        => $date,
			'DATA_SPRZEDAZY'          => $date,
			'DATA_WPLYWU'             => $date,
			'TERMIN'                  => $deadline,
			'NUMER'                   => $invoice->full_number,
			'KOREKTA'                 => 'Nie',
			'KOREKTA_NUMER'           => '',
			'WEWNETRZNA'              => 'Nie',
			'FISKALNA'                => 'Nie',
			'DETALICZNA'              => 'Nie',
			'EKSPORT'                 => 'Nie',
			'IDENTYFIKATOR_KSIEGOWY'  => $invoice->id,
			'TYP_PODMIOTU'            => 'kontrahent',
			'PODMIOT'                 => $user->id,
			'NAZWA1'                  => $name,
			'NAZWA2'                  => '',
			'NAZWA3'                  => '',
			'NIP_KRAJ'                => 'PL',
			'NIP'                     => $vatId,
			'ULICA'                   => $street,
			'NR_DOMU'                 => '',
			'NR_LOKALU'               => '',
			'MIASTO'                  => $city,
			'KOD_POCZTOWY'            => $zip,
			'KRAJ'                    => $country,
			'FORMA_PLATNOSCI'         => 'przelew',
			'FORMA_PLATNOSCI_ID'      => 'A',
			'DEKLARACJA_VATUE'        => 'Nie',
			'WALUTA'                  => 'PLN',
			'KURS_WALUTY'             => '--',
			'DATA_KURSU'              => $date,
			'NOTOWANIE_WALUTY_ILE'    => '1.0000',
			'NOTOWANIE_WALUTY_ZA_ILE' => '1',
			'POZYCJE'                 => [
				'POZYCJA' => [
					'STAWKA_VAT'      => $vatRate,
					'STATUS_VAT'      => $vatStatus,
					'NETTO'           => $priceNet,
					'VAT'             => $vatValue,
					'NETTO_SYS'       => $priceNet,
					'VAT_SYS'         => $vatValue,
					'UWZ_W_PROPORCJI' => $uwz,
				],
			],
			'PLATNOSCI'               => [
				'PLATNOSC' => [
					'ID_ZRODLA_PLAT'               => '',
					'TERMIN_PLAT'                  => $deadline,
					'FORMA_PLATNOSCI_PLAT'         => 'przelew',
					'FORMA_PLATNOSCI_ID_PLAT'      => 'A',
					'KWOTA_PLAT'                   => "$priceGross",
					'WALUTA_PLAT'                  => 'PLN',
					'PLATNOSC_TYP_PODMIOTU'        => 'kontrahent',
					'PLATNOSC_PODMIOT'             => $user->id,
					'PLATNOSC_PODMIOT_ID'          => $user->id,
					'PLATNOSC_PODMIOT_NIP'         => $vatId,
					'KURS_WALUTY_PLAT'             => 'NBP',
					'NOTOWANIE_WALUTY_ILE_PLAT'    => '1',
					'NOTOWANIE_WALUTY_ZA_ILE_PLAT' => '1',
					'KWOTA_PLN_PLAT'               => "$priceGross",
					'KIERUNEK'                     => 'przychód',
					'PODLEGA_ROZLICZENIU'          => 'tak',
					'KONTO'                        => '',
					'NIE_NALICZAJ_ODSETEK'         => 'Nie',
					'PRZELEW_SEPA'                 => 'Nie',
					'DATA_KURSU_PLAT'              => $deadline,
					'WALUTA_DOK'                   => 'PLN',
					'PLAT_ELIXIR_O1'               => '',
					'PLAT_ELIXIR_O2'               => '',
					'PLAT_ELIXIR_O3'               => '',
					'PLAT_ELIXIR_O4'               => '',
				],
			],
		];


		return [$contractor, $invoice];
	}

	protected function price($number)
	{
		return number_format($number, 2, '.', '');
	}
}
