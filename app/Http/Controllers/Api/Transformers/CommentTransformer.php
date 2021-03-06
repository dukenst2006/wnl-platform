<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
	protected $parent;

	protected $availableIncludes = ['profiles'];

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}


	public function transform(Comment $comment)
	{
		$data = [
			'id'         => $comment->id,
			'text'       => $comment->text,
			'created_at' => $comment->created_at->timestamp,
			'updated_at' => $comment->updated_at->timestamp,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}

	public function includeProfiles(Comment $comment)
	{
		$profile = $comment->user->profile;

		return $this->item($profile, new UserProfileTransformer(['comments' => $comment->id]), 'profiles');
	}
}
