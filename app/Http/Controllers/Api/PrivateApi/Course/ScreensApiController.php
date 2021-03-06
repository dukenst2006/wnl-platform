<?php

namespace App\Http\Controllers\Api\PrivateApi\Course;


use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\ScreenTransformer;
use App\Http\Requests\Course\UpdateScreen;
use App\Http\Requests\Course\DeleteScreen;
use App\Models\Screen;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;

class ScreensApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.screens');
	}

	public function delete(DeleteScreen $request)
	{
		$id = $request->route('id');

		$screen = Screen::find($id);
		if (!$screen) {
			return $this->respondNotFound();
		}

		Screen::destroy($id);

		return $this->respondOk();
	}

	public function put(UpdateScreen $request)
	{
		$screen = Screen::find($request->route('id'));

		if (!$screen) {
			return $this->respondNotFound();
		}

		$screen->update([
			'content' => $request->input('content'),
			'meta' => json_decode($request->input('meta')),
			'type' => $request->input('type'),
			'name' => $request->input('name'),
		]);

		return $this->respondOk();
	}

	public function patch(UpdateScreen $request)
	{
		$screen = Screen::find($request->route('id'));

		if (!$screen) {
			return $this->respondNotFound();
		}

		$screen->update($request->all());

		return $this->respondOk();
	}

	public function post(UpdateScreen $request)
	{
		$screen = Screen::create($request->all());

		$resource = new Item($screen, new ScreenTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}
}
