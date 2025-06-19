<?php
namespace Model;

use Model\ActiveRecord;

class Calibre extends ActiveRecord
{
    protected static $tabla = 'guzman_calibres';
    protected static $idTabla = 'calibre_id';
    protected static $columnasDB = [
        'calibre_id',
        'calibre_nombre',
        'calibre_descripcion',
        'calibre_situacion'
    ];

    public $calibre_id;
    public $calibre_nombre;
    public $calibre_descripcion;
    public $calibre_situacion;

    public function __construct($args = [])
    {
        $this->calibre_id = $args['calibre_id'] ?? null;
        $this->calibre_nombre = $args['calibre_nombre'] ?? '';
        $this->calibre_descripcion = $args['calibre_descripcion'] ?? '';
        $this->calibre_situacion = $args['calibre_situacion'] ?? 1;
    }
}