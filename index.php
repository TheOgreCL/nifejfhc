<?php

require_once __DIR__ . '/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (isset($_GET['generar'])) {

    // Cargar XML
    $archivoXML = __DIR__ . '/factura.xml';

    $xml = new DOMDocument();

    if (!$xml->load($archivoXML)) {
        die("No se pudo cargar el archivo XML.");
    }

    // Preparar XPath para leer el XML del SII
    $xpath = new DOMXPath($xml);

    // Buscar documento
    $documento = $xpath->query("//*[local-name()='Documento']")->item(0);

    if (!$documento) {
        die("No se encontró el documento DTE.");
    }

    // Función para obtener valores del XML
    function obtenerDato($xpath, $contexto, $ruta) {
        $resultado = $xpath->query($ruta, $contexto);

        if ($resultado->length > 0) {
            return trim($resultado->item(0)->textContent);
        }

        return "";
    }

    // Obtener datos generales
    $encabezado = $xpath->query("./*[local-name()='Encabezado']", $documento)->item(0);

    if (!$encabezado) {
        die("No se encontró el encabezado del documento.");
    }

    // Datos del documento
    $folio = obtenerDato($xpath, $encabezado, "./*[local-name()='IdDoc']/*[local-name()='Folio']");
    $fecha = obtenerDato($xpath, $encabezado, "./*[local-name()='IdDoc']/*[local-name()='FchEmis']");
    $tipoDTE = obtenerDato($xpath, $encabezado, "./*[local-name()='IdDoc']/*[local-name()='TipoDTE']");

    // Datos del emisor
    $emisor = $xpath->query("./*[local-name()='Emisor']", $encabezado)->item(0);

    $rutEmisor = obtenerDato($xpath, $emisor, "./*[local-name()='RUTEmisor']");
    $razonSocial = obtenerDato($xpath, $emisor, "./*[local-name()='RznSoc']");
    $giro = obtenerDato($xpath, $emisor, "./*[local-name()='GiroEmis']");
    $telefono = obtenerDato($xpath, $emisor, "./*[local-name()='Telefono']");
    $correo = obtenerDato($xpath, $emisor, "./*[local-name()='CorreoEmisor']");
    $direccion = obtenerDato($xpath, $emisor, "./*[local-name()='DirOrigen']");
    $comuna = obtenerDato($xpath, $emisor, "./*[local-name()='CmnaOrigen']");
    $ciudad = obtenerDato($xpath, $emisor, "./*[local-name()='CiudadOrigen']");

    // Totales
    $totales = $xpath->query("./*[local-name()='Totales']", $encabezado)->item(0);

    $neto = obtenerDato($xpath, $totales, "./*[local-name()='MntNeto']");
    $iva = obtenerDato($xpath, $totales, "./*[local-name()='IVA']");
    $total = obtenerDato($xpath, $totales, "./*[local-name()='MntTotal']");

    // Forma de pago
    $formaPago = obtenerDato($xpath, $encabezado, "./*[local-name()='IdDoc']/*[local-name()='FmaPago']");

    if ($formaPago == "1") {
        $formaPagoTexto = "Contado";
    } elseif ($formaPago == "2") {
        $formaPagoTexto = "Crédito";
    } elseif ($formaPago == "3") {
        $formaPagoTexto = "Sin costo";
    } else {
        $formaPagoTexto = "No informado";
    }

    // Detalles de productos
    $detalles = [];
    $nodosDetalle = $xpath->query("./*[local-name()='Detalle']", $documento);

    foreach ($nodosDetalle as $detalle) {
        $detalles[] = [
            "nombre" => obtenerDato($xpath, $detalle, "./*[local-name()='NmbItem']"),
            "cantidad" => obtenerDato($xpath, $detalle, "./*[local-name()='QtyItem']"),
            "precio" => obtenerDato($xpath, $detalle, "./*[local-name()='PrcItem']"),
            "subtotal" => obtenerDato($xpath, $detalle, "./*[local-name()='MontoItem']")
        ];
    }

    // Si no hay detalles formales, respaldamos con el ítem del TED
    $tedNode = $xpath->query(".//*[local-name()='TED']", $documento)->item(0);
    if (empty($detalles) && $tedNode) {
        $itemTed = obtenerDato($xpath, $tedNode, ".//*[local-name()='IT1']");
        if (!empty($itemTed)) {
            $detalles[] = [
                "nombre" => $itemTed,
                "cantidad" => "1",
                "precio" => $neto !== "" ? $neto : $total,
                "subtotal" => $neto !== "" ? $neto : $total
            ];
        }
    }

    // Datos del timbre electrónico (TED)
    $tedContenido = "";
    if ($tedNode) {
        $tedContenido = $tedNode->C14N(); // Extrae todo el bloque XML del TED con formato canónico
    }
    $tedUrlEncoded = urlencode($tedContenido);

    // Formatear valores
    function formatoMonto($valor) {
        return number_format((float)$valor, 0, ',', '.');
    }

    // Formatear fecha
    $fechaFormato = $fecha;
    if ($fecha != "") {
        $fechaFormato = date("d-m-Y", strtotime($fecha));
    }

    // Ruta de tu archivo de logo
    $rutaLogo = __DIR__ . '/think2process.png'; 
    $logoBase64 = '';

    if (file_exists($rutaLogo)) {
        $tipoLogo = pathinfo($rutaLogo, PATHINFO_EXTENSION);
        $dataLogo = file_get_contents($rutaLogo);
        $logoBase64 = 'data:image/' . $tipoLogo . ';base64,' . base64_encode($dataLogo);
    }

    // Generar HTML
    ob_start();
    require __DIR__ . '/plantilla_ticket.php';
    $html = ob_get_clean();

    // Configuración Dompdf
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'Arial');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');

    // 80mm de ancho exacto -> 80 * 2.83465 = 226.77 pt
    $paperWidth  = 80 * 2.83465;
    $paperHeight = 220 * 2.83465; // Altura generosa para evitar cortes

    $dompdf->setPaper([0, 0, $paperWidth, $paperHeight], 'portrait');
    $dompdf->render();

    // Mostrar PDF en el navegador
    $dompdf->stream("factura_" . $folio . ".pdf", [
        "Attachment" => false
    ]);
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generador de Boleta 80mm</title>
    <style>
        body {
            background-color: #0f172a;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .btn-generar {
            background-color: #1f4ad9;
            color: white;
            padding: 16px 32px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
            transition: background 0.2s, transform 0.1s;
        }
        .btn-generar:hover {
            background-color: #2c10aa;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <a href="index.php?generar=1" class="btn-generar" target="_blank">Generar PDF Boleta 80mm</a>

</body>
</html>