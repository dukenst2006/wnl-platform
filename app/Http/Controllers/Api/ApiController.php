<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Api\Concerns\GeneratesApiResponses;
use App\Http\Controllers\Api\Serializer\ApiJsonSerializer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

abstract class ApiController extends Controller
{
	use GeneratesApiResponses;

	protected $fractal;
	protected $request;
	protected $resourceName;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->fractal = new Manager();
		$this->fractal->setRecursionLimit(5);
		$this->fractal->setSerializer(new ApiJsonSerializer());
		if ($this->request->has('include')) {
			$this->fractal->parseIncludes($this->request->get('include'));
		}
	}

	public function get($id)
	{
		$resourceSingular = str_singular($this->resourceName);
		$resourceStudly = studly_case($resourceSingular);
		$modelName = 'App\Models\\' . $resourceStudly;
		$transformerName = 'App\Http\Controllers\Api\Transformers\\' . $resourceStudly . 'Transformer';
		$models = $modelName::find($id);
		$resource = new Item($models, new $transformerName, $resourceSingular);
		$data = $this->fractal->createData($resource)->toArray();

		return response()->json($data);
	}
}