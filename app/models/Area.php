<?php

class Area extends Eloquent
{
    protected $table = "areas";
    protected $fillable = ['id', 'nombre', 'nemonico', 'id_int', 'id_ext', 'imagen', 'imagenc','imagenp', 'estado'];
    /**
     * Cargos relationship
     */
    public function usuarios() {
        return $this->hasMany('User');
    }

}
