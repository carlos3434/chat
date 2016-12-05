<?php namespace LaravelRealtimeChat\Repositories\User;

interface UserRepository {
    
    /**
     * Fetch a record by id
     * 
     * @param $id
     */
    public function getById($id);

    /**
     * Fetch all users except the one specified by the id
     *
     * @param $id;
     */
    public function getAllExcept($id);

    /**
     * cargar todos los musuarios excepto el $id y del area : $area_id y estado 1
     *
     * @param $id;
     */
    public function getAllExceptFromArea($id, $area_id);
}
