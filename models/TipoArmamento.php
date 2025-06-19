<?php
namespace Model;

use Model\ActiveRecord;

class TipoArmamento extends ActiveRecord
{
    protected static $tabla = 'guzman_tipos_armamento';
    protected static $idTabla = 'tipo_id';
    protected static $columnasDB = [
        'tipo_id',
        'tipo_nombre',
        'tipo_descripcion',
        'tipo_categoria',
        'tipo_situacion'
    ];

    public $tipo_id;
    public $tipo_nombre;
    public $tipo_descripcion;
    public $tipo_categoria;
    public $tipo_situacion;

    public function __construct($args = [])
    {
        $this->tipo_id = $args['tipo_id'] ?? null;
        $this->tipo_nombre = $args['tipo_nombre'] ?? '';
        $this->tipo_descripcion = $args['tipo_descripcion'] ?? '';
        $this->tipo_categoria = $args['tipo_categoria'] ?? '';
        $this->tipo_situacion = $args['tipo_situacion'] ?? 1;
    }
}