<?php
// Escapar datos para mostrarlos de forma segura
$e = function($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
};
// Asegurar que la variable del TED esté lista para la URL
$tedUrlEncoded = isset($tedContenido) ? urlencode($tedContenido) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>

    /* Márgenes de página seguros*/
    @page {
        margin: 4mm; 
    }
    body {
        font-family: Arial, sans-serif;
        font-size: 8.5pt;
        color: #000;
        margin: 0;
        padding: 0 4px;
        line-height: 1.2;
    }
    .contenedor {
        width: 100%;
        max-width: 212pt; /* Ancho exacto papel 80mm */
        margin: 0 auto;
        box-sizing: border-box;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .bold { font-weight: bold; }
    
    /* Encabezado */
    .encabezado {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; 
        margin-bottom: 6px;
    }
    .encabezado td {
        vertical-align: middle;
        padding: 0;
    }
    .logo-cell {
        width: 48%;
        text-align: left;
        vertical-align: middle;
    }
    .datos-empresa-cell {
        width: 52%;
        text-align: center;
        vertical-align: middle;
    }
    
    /* Recuadro rojo DTE */
    .datos-empresa {
        width: 100%;
        border: 1.5px solid #ff3333;
        text-align: center;
        padding: 3px 2px;
        color: #ff3333;
        box-sizing: border-box;
    }
    .datos-empresa h2 {
        font-size: 8.5pt;
        margin: 1px 0;
    }
    .datos-empresa .titulo {
        font-size: 8.5pt;
        font-weight: bold;
        margin: 1px 0;
    }
    .sii-fuera {
        display: block;
        width: 100%;
        text-align: center;
        font-weight: bold;
        font-size: 8.5pt;
        color: #ff3333;
        margin-top: 3px;
        margin-bottom: 6px;
    }

    /* Datos del Emisor */
    .datos-emisor {
        text-align: center;
        margin-bottom: 6px;
        font-size: 8pt;
    }
    .datos-emisor .giro-txt {
        font-size: 8pt;
        font-weight: bold;
        margin-bottom: 2px;
    }
    .datos-emisor h3 {
        margin: 2px 0;
        font-size: 9pt;
        font-weight: normal;
    }

    /* Datos Generales */
    .datos-generales {
        margin-bottom: 6px;
        font-size: 8pt;
        line-height: 1.3;
    }
    .datos-generales div {
        margin: 1px 0;
        font-weight: normal;
    }

    /* Líneas divisorias */
    .linea-doble {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        height: 1px;
        margin: 4px 0;
    }

    /* Tabla de Productos / Ítems */
    .productos {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2px;
        margin-bottom: 4px;
        font-size: 8pt;
    }
    .productos th {
        padding: 3px 2px;
        text-align: left;
        font-size: 8pt;
        font-weight: bold;
    }
    .productos td {
        padding: 4px 2px;
        vertical-align: top;
        font-weight: normal;
    }
    .derecha { text-align: right; }

    /* Recuadro de Totales */
    .totales-container {
        width: 100%;
        text-align: right;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .totales-box {
        display: inline-block;
        width: 65%;
        border: 1px solid #000;
        padding: 3px 5px;
        text-align: left;
        box-sizing: border-box;
    }
    .totales-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8pt;
    }
    .totales-table td {
        padding: 1px 0;
    }
    .total-final {
        font-size: 8.5pt;
        font-weight: bold;
    }

    /* Sección Factura Cedible */
    .cedible-container {
        margin-top: 10px;
        margin-bottom: 6px;
        border: 1px solid #000;
        padding: 4px 5px;
        box-sizing: border-box;
        font-size: 6.5pt;
    }
    .cedible-tabla {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 4px;
    }
    .cedible-tabla td {
        padding: 2px 0;
        vertical-align: middle;
        font-size: 6.5pt;
        font-weight: normal;
    }
    .cedible-linea {
        border-bottom: 0.8px solid #000;
        height: 10px;
    }
    .cedible-glosa {
        border-top: 0.8px solid #000;
        padding-top: 3px;
        text-align: justify;
        line-height: 1.1;
        font-size: 5.8pt;
        font-weight: normal;
    }
    .cedible-titulo-rojo {
        text-align: center;
        color: #ff3333;
        font-size: 9pt;
        font-weight: bold;
        margin-top: 4px;
        margin-bottom: 8px;
    }

    /* Código de barras / Timbre */
    .timbre-container {
        margin-top: 8px;
        text-align: center;
    }
    .timbre-box {
        width: 100%;
        text-align: center;
        margin-bottom: 4px;
    }

</style>

</head>
<body>

<div class="contenedor">

    <!--Encabezado-->
    <table class="encabezado">
        <tr>
            <td class="logo-cell" style="text-align: left;">
                <img src="<?= $logoBase64 ?>" style="max-width: 130px; max-height: 100px; width: auto; height: auto; object-fit: contain; display: block;" alt="Logo">
            </td>
            <td class="datos-empresa-cell">
                <div class="datos-empresa">
                    <h2>R.U.T.: <?= $e($rutEmisor) ?></h2>
                    <div class="titulo">FACTURA ELECTRÓNICA</div>
                    <h2>N° <?= $e($folio) ?></h2>
                </div>
                <div class="sii-fuera">S.I.I. - <?= $e($comuna) ?></div>
            </td>
        </tr>
    </table>

    <!--Datos emisor-->
    <div class="datos-emisor">
        <div class="giro-txt"><?= $e($giro) ?></div>
        <h3><?= $e($razonSocial) ?></h3>
    </div>

    <!--Datos generales-->
    <div class="datos-generales">
        <div>Dirección: <?= $e($direccion) ?></div>
        <div>Comuna: <?= $e($comuna) ?></div>
        <div>Ciudad: <?= $e($ciudad) ?></div>
        <div>Fono: <?= $e($telefono) ?></div>
        <div>Fecha: <?= $e($fechaFormato) ?></div>
        <div>Forma de Pago: <?= $e($formaPagoTexto) ?></div>
        <div>Sucursal: </div>
    </div>

    <!-- Líneas divisorias -->
    <div class="linea-doble"></div>

    <!--Tabla de productos e items-->
    <table class="productos">
        <thead>
            <tr>
                <th width="15%">UND</th>
                <th width="40%">ÍTEM</th>
                <th width="20%" class="derecha">V.UNI</th>
                <th width="25%" class="derecha">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $producto): ?>
            <tr>
                <td><?= $e($producto["cantidad"]) ?></td>
                <td>
                    <?= $e($producto["nombre"]) ?><br>
                    <span style="font-size: 7.5pt; color: #444; font-weight: normal;">AFECTO</span>
                </td>
                <td class="derecha">$<?= formatoMonto($producto["precio"]) ?></td>
                <td class="derecha">$<?= formatoMonto($producto["subtotal"]) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="linea-doble"></div>

    <!--Totales del cuadrito-->
    <div class="totales-container">
        <div class="totales-box">
            <table class="totales-table">
                <tr>
                    <td width="42%"><strong>NETO</strong></td>
                    <td width="8%"><strong>$</strong></td>
                    <td width="8%"><strong>:</strong></td>
                    <td class="derecha" width="42%">$<?= formatoMonto($neto) ?></td>
                </tr>
                <tr>
                    <td><strong>IVA(19%)</strong></td>
                    <td><strong>$</strong></td>
                    <td><strong>:</strong></td>
                    <td class="derecha">$<?= formatoMonto($iva) ?></td>
                </tr>
                <tr class="total-final">
                    <td><strong>TOTAL</strong></td>
                    <td><strong>$</strong></td>
                    <td><strong>:</strong></td>
                    <td class="derecha">$<?= formatoMonto($total) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!--Seccion cedible-->
    <div class="cedible-container">
        <table class="cedible-tabla">
            <tr>
                <td style="width: 12%; font-weight: normal;">Nombre:</td>
                <td style="width: 88%;" class="cedible-linea"></td>
            </tr>
        </table>
        <table class="cedible-tabla">
            <tr>
                <td style="width: 10%; font-weight: normal;">R.U.T.:</td>
                <td style="width: 45%;" class="cedible-linea"></td>
                <td style="width: 12%; text-align: right; font-weight: normal;">Firma:</td>
                <td style="width: 33%;" class="cedible-linea"></td>
            </tr>
        </table>
        <table class="cedible-tabla" style="margin-bottom: 2px;">
            <tr>
                <td style="width: 10%; font-weight: normal;">Fecha:</td>
                <td style="width: 45%;" class="cedible-linea"></td>
                <td style="width: 15%; text-align: right; font-weight: normal;">Recinto:</td>
                <td style="width: 30%;" class="cedible-linea"></td>
            </tr>
        </table>
        <div class="cedible-glosa">
            El acuse de recibo que se declara en este acto, de acuerdo a lo dispuesto en la letra b) del Art. 4°, y la letra c) del Art. 5° de la ley 19.983, acredita que la entrega de mercaderías o servicio(s) prestado(s) ha(n) sido recibido(s).
        </div>
    </div>
    <div class="cedible-titulo-rojo">CEDIBLE</div>

    <!--Timbre y verificacion en la parte de abajo-->
    <div class="timbre-container">
        <div class="timbre-box">
            <?php if (!empty($tedUrlEncoded)): ?>
                <!--Generador PDF417-->
                <img src="https://barcode.tec-it.com/barcode.ashx?data=<?= $tedUrlEncoded ?>&code=PDF417&imagetype=Png&dpi=300" 
     style="width: 95%; max-height: 65px; object-fit: contain; display: block; margin: 0 auto;" 
     alt="Timbre SII PDF417">
            <?php else: ?>
                <span style="font-size: 6pt; color: #666;">[ TED NO ENCONTRADO EN EL XML ]</span>
            <?php endif; ?>
        </div>
        <div style="font-size: 7.5pt; font-weight: bold; margin-top: 4px;">Timbre Electrónico S.I.I.</div>
        <div style="font-size: 7pt; margin-top: 2px;">Verifique Documentos en: https://www.dtechile.cl</div>
    </div>

</div>

</body>
</html>