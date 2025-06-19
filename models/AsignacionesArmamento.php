<?php
namespace Model;

use Model\ActiveRecord;

class AsignacionArmamento extends ActiveRecord
{
    protected static $tabla = 'guzman_asignaciones_armamento';
    protected static $idTabla = 'asignacion_id';
    protected static $columnasDB = [
        'asignacion_id',
        'asignacion_arma',
        'asignacion_personal',
        'asignacion_fecha_asignacion',
        'asignacion_fecha_devolucion',
        'asignacion_motivo',
        'asignacion_estado',
        'asignacion_usuario',
        'asignacion_situacion'
    ];

    public $asignacion_id;
    public $asignacion_arma;
    public $asignacion_personal;
    public $asignacion_fecha_asignacion;
    public $asignacion_fecha_devolucion;
    public $asignacion_motivo;
    public $asignacion_estado;
    public $asignacion_usuario;
    public $asignacion_situacion;

    public function __construct($args = [])
    {
        $this->asignacion_id = $args['asignacion_id'] ?? null;
        $this->asignacion_arma = $args['asignacion_arma'] ?? '';
        $this->asignacion_personal = $args['asignacion_personal'] ?? '';
        $this->asignacion_fecha_asignacion = $args['asignacion_fecha_asignacion'] ?? date('Y-m-d');
        $this->asignacion_fecha_devolucion = $args['asignacion_fecha_devolucion'] ?? null;
        $this->asignacion_motivo = $args['asignacion_motivo'] ?? '';
        $this->asignacion_estado = $args['asignacion_estado'] ?? 'ASIGNADO';
        $this->asignacion_usuario = $args['asignacion_usuario'] ?? '';
        $this->asignacion_situacion = $args['asignacion_situacion'] ?? 1;
    }
}