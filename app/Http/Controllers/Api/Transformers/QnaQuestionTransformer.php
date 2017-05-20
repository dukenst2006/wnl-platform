<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Lesson;
use App\Models\QnaQuestion;
use League\Fractal\TransformerAbstract;


class QnaQuestionTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['answers', 'tags', 'users'];
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(QnaQuestion $question)
	{
		$data = [
			'id'         => $question->id,
			'text'       => $question->text,
			'created_at' => $question->created_at->timestamp,
			'updated_at' => $question->updated_at->timestamp,
		];

		if ($this->parent) {
			$data['lessons'] = $this->parent;
		}

		return $data;
	}

	public function includeAnswers(QnaQuestion $question)
	{
		$answers = $question->answers;

		return $this->collection($answers, new QnaAnswerTransformer, 'answers');
	}

	public function includeTags(QnaQuestion $question)
	{
		$tags = $question->tags;

		return $this->collection($tags, new TagTransformer, 'tags');
	}

	public function includeUsers(QnaQuestion $question)
	{
		$user = $question->user->profile;

		return $this->collection([$user], new UserProfileTransformer(['qna_questions' => $question->id]), 'users');
	}
}