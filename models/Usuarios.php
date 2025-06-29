<?php

namespace Model;

use Model\ActiveRecord;

class Usuarios extends ActiveRecord
{
    protected static $tabla = 'guzman_usuarios';
    protected static $idTabla = 'usuario_id';
    protected static $columnasDB = [
        'usuario_id',
        'usuario_nombre', 
        'usuario_apellido',
        'usuario_dpi',
        'usuario_correo',
        'usuario_contra',
        'usuario_fotografia',
        'usuario_situacion'
    ];

    public $usuario_id;
    public $usuario_nombre;
    public $usuario_apellido;
    public $usuario_dpi;
    public $usuario_correo;
    public $usuario_contra;
    public $usuario_fotografia;
    public $usuario_situacion;

    public function __construct($args = [])
    {
        $this->usuario_id = $args['usuario_id'] ?? null;
        $this->usuario_nombre = $args['usuario_nombre'] ?? '';
        $this->usuario_apellido = $args['usuario_apellido'] ?? '';
        $this->usuario_dpi = $args['usuario_dpi'] ?? '';
        $this->usuario_correo = $args['usuario_correo'] ?? '';
        $this->usuario_contra = $args['usuario_contra'] ?? '';
        $this->usuario_fotografia = $args['usuario_fotografia'] ?? null;
        $this->usuario_situacion = $args['usuario_situacion'] ?? 1;
    }
}