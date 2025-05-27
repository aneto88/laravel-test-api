<?php

namespace App\DTOs;

class FavoriteProductDTO
{
    public $id;
    public $productId;
    public $title;
    public $price;
    public $image;
    public $review;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['product_id'];
        $this->title = $data['title'];
        $this->price = $data['price'];
        $this->image = $data['image'];
        $this->review = $data['review'];
    }

    public static function fromModel($model)
    {
        return new self([
            'id' => $model->id,
            'product_id' => $model->product_id,
            'title' => $model->title,
            'price' => $model->price,
            'image' => $model->image,
            'review' => $model->review
        ]);
    }

    public static function fromCollection($collection)
    {
        return $collection->map(function ($model) {
            return self::fromModel($model);
        });
    }
}
