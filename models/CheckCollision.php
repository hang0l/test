<?php

namespace app\models;
use Yii;
use yii\base\Model;
use app\models\Figure;

class CheckCollision extends Model
{
    public $id;
    public $x;
    public $y;
    public $d;
    public $name;

    public function rules()
	{
		return [
		[['id', 'x', 'y', 'd', 'name'], 'required']
		];
	}

	public function returnIdToDelete($firstId, $secondId): array
	{
		$response = Yii::$app->response;
		$response->format = \yii\web\Response::FORMAT_JSON;
		return $response->data =
			['id' => $this->getIdToDelete($firstId, $secondId)];
	}

    public function getShapeNumber($figureModel): int
	{
		switch($figureModel->shape)
		{
			case 'circle': return 10;
			case 'hexagon': return 6;
			case 'square': return 4;
			case 'triangle': return 3;
		}
	}

	/**
	 * @param $firstId
	 * @param $secondId
	 * @return mixed|null
	 */
	public function getIdToDelete($firstId, $secondId)
	{
		$figureModelOne = Figure::findOne($firstId);
		$figureModelTwo = Figure::findOne($secondId);
		if ($figureModelOne->player_id !== $figureModelTwo->player_id) {
			if ($this->getShapeNumber($figureModelOne) > $this->getShapeNumber($figureModelTwo)) {
				return $figureModelTwo->id;
			} else if ($this->getShapeNumber($figureModelOne) < $this->getShapeNumber($figureModelTwo)) {
				return $figureModelOne->id;
			}
		}
	}
}
?>
