<?php

function calcularTempoEstacionado($dataEntrada)
{
    $entrada = new DateTime($dataEntrada);
    $agora = new DateTime();

    $intervalo = $entrada->diff($agora);

    return sprintf(
        '%02dh %02dmin',
        ($intervalo->days * 24) + $intervalo->h,
        $intervalo->i
    );
}


function formatarTempo($minutos)
{
    $horas = floor($minutos / 60);
    $minutosRestantes = $minutos % 60;

    return sprintf('%02dh %02dmin', $horas, $minutosRestantes);
}
