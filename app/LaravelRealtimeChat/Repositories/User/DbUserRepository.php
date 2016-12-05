<?php namespace LaravelRealtimeChat\Repositories\User;

use LaravelRealtimeChat\Repositories\DbRepository;

class DbUserRepository extends DbRepository implements UserRepository {

    /**
     * @var User
     */
    private $model;

    public function __construct(\User $model)
    {
        $this->model = $model;
    }

    public function getAllExcept($id)
    {
        return $this->model->where('id', '<>', $id)->get();
    }
    /**
     * cargar todos los musuarios excepto el $id y del area : $area_id y estado 1
     */
    public function getAllExceptFromArea($id, $area_id)
    {
        return $this->model
        ->where('id', '<>', $id)
        ->where('estado', 1)
        ->where('area_id', $area_id)
        ->get();
    }
}
