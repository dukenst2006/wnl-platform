<?php


namespace App\Http\Controllers\Api\Transformers;


use DB;
use App\Models\Lesson;
use League\Fractal\TransformerAbstract;

class LessonTransformer extends TransformerAbstract
{
	protected $availableIncludes = ['screens', 'tags', 'qna_questions'];

	protected $editionId;

	public function __construct($editionId = null)
	{
		$this->editionId = $editionId;
	}

	public function transform(Lesson $lesson)
	{
		$data = [
			'id'       => $lesson->id,
			'name'     => $lesson->name,
			'group_id' => $lesson->group_id,
			'groups'   => $lesson->group_id,
			'editions' => $lesson->group->course->id,
		];

		if ($this->editionId !== null) {
			$data['isAvailable'] = $lesson->isAvailable($this->editionId);
		}

		return $data;
	}

	public function includeScreens(Lesson $lesson)
	{
		$screens = $lesson->screens;

		return $this->collection($screens, new ScreenTransformer, 'screens');
	}

	public function includeTags(Lesson $lesson)
	{
		$tags = $lesson->tags;

		return $this->collection($tags, new TagTransformer(['lessons' => $lesson->id]), 'tags');
	}

	public function includeQnaQuestions(Lesson $lesson)
	{
		$questions = $lesson->questions;

		return $this->collection($questions, new QnaQuestionTransformer($lesson->id), 'qna_questions');
	}
}
