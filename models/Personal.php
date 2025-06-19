<?php
namespace Model;

use Model\ActiveRecord;

class Personal extends ActiveRecord
{
    protected static $tabla = 'guzman_personal';
    protected static $idTabla = 'personal_id';
    protected static $columnasDB = [
        'personal_id',
        'personal_nombres',
        'personal_apellidos',
        'personal_grado',
        'personal_unidad',
        'personal_dpi',
        'personal_situacion'
    ];

    public $personal_id;
    public $personal_nombres;
    public $personal_apellidos;
    public $personal_grado;
    public $personal_unidad;
    public $personal_dpi;
    public $personal_situacion;

    public function __construct($args = [])
    {
        $this->personal_id = $args['personal_id'] ?? null;
        $this->personal_nombres = $args['personal_nombres'] ?? '';
        $this->personal_apellidos = $args['personal_apellidos'] ?? '';
        $this->personal_grado = $args['personal_grado'] ?? '';
        $this->personal_unidad = $args['personal_unidad'] ?? '';
        $this->personal_dpi = $args['personal_dpi'] ?? '';
        $this->personal_situacion = $args['personal_situacion'] ?? 1;
    }
}