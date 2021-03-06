<?php

namespace App\Http\Controllers\Api\PrivateApi\Qna;

use App\Http\Controllers\Api\Transformers\QnaAnswerTransformer;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Qna\PostAnswer;
use League\Fractal\Resource\Item;
use Illuminate\Http\Request;
use App\Models\QnaQuestion;
use Auth;

class AnswersApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.answers');
	}

	public function post(PostAnswer $request)
	{
		$questionId = $request->get('question_id');
		$text = $request->get('text');
		$user = Auth::user();

		$question = QnaQuestion::find($questionId);

		if (!$question) {
			return $this->respondInvalidInput('Question does not exist.');
		}

		$answer = $question->answers()->create([
			'text'    => $text,
			'user_id' => $user->id,
		]);

		$resource = new Item($answer, new QnaAnswerTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}
}
