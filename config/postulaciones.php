<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Días de inactividad antes de recordar seguimiento
    |--------------------------------------------------------------------------
    |
    | Cantidad de días sin cambios en una postulación "Postulado" antes de
    | que se le recuerde al usuario que haga seguimiento.
    |
    */

    'dias_recordatorio_seguimiento' => (int) env('POSTULACIONES_DIAS_RECORDATORIO', 7),

];
