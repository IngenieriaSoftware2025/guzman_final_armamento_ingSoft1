<?php
namespace Model;

use Model\ActiveRecord;

class Armamento extends ActiveRecord
{
    protected static $tabla = 'guzman_armamento';
    protected static $idTabla = 'arma_id';
    protected static $columnasDB = [
        'arma_id',
        'arma_numero_serie',
        'arma_tipo',
        'arma_calibre', 
        'arma_estado',
        'arma_fecha_ingreso',
        'arma_almacen',
        'arma_observaciones',
        'arma_situacion'
    ];

    public $arma_id;
    public $arma_numero_serie;
    public $arma_tipo;
    public $arma_calibre;
    public $arma_estado;
    public $arma_fecha_ingreso;
    public $arma_almacen;
    public $arma_observaciones;
    public $arma_situacion;

    public function __construct($args = [])
    {
        $this->arma_id = $args['arma_id'] ?? null;
        $this->arma_numero_serie = $args['arma_numero_serie'] ?? '';
        $this->arma_tipo = $args['arma_tipo'] ?? '';
        $this->arma_calibre = $args['arma_calibre'] ?? '';
        $this->arma_estado = $args['arma_estado'] ?? 'BUEN_ESTADO';
        $this->arma_fecha_ingreso = $args['arma_fecha_ingreso'] ?? date('Y-m-d');
        $this->arma_almacen = $args['arma_almacen'] ?? '';
        $this->arma_observaciones = $args['arma_observaciones'] ?? '';
        $this->arma_situacion = $args['arma_situacion'] ?? 1;
    }
}