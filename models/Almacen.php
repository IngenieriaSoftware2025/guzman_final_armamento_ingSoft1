<?php
namespace Model;

use Model\ActiveRecord;

class Almacen extends ActiveRecord
{
    protected static $tabla = 'guzman_almacenes';
    protected static $idTabla = 'almacen_id';
    protected static $columnasDB = [
        'almacen_id',
        'almacen_nombre',
        'almacen_ubicacion',
        'almacen_responsable',
        'almacen_situacion'
    ];

    public $almacen_id;
    public $almacen_nombre;
    public $almacen_ubicacion;
    public $almacen_responsable;
    public $almacen_situacion;

    public function __construct($args = [])
    {
        $this->almacen_id = $args['almacen_id'] ?? null;
        $this->almacen_nombre = $args['almacen_nombre'] ?? '';
        $this->almacen_ubicacion = $args['almacen_ubicacion'] ?? '';
        $this->almacen_responsable = $args['almacen_responsable'] ?? null;
        $this->almacen_situacion = $args['almacen_situacion'] ?? 1;
    }
}