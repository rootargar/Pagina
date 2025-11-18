<?php
// Incluir el archivo de conexión
require_once 'conexion.php';

// Inicializar variables
$resultados = [];
$busqueda = '';
$mensaje = '';
$totalResultados = 0;

// Configuración de paginación
$resultadosPorPagina = 10;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$paginaActual = max(1, $paginaActual);

// Procesar la búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['busqueda'])) {
    $busqueda = trim($_POST['busqueda']);
    $paginaActual = 1;
} elseif (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
    $busqueda = trim($_GET['busqueda']);
}

if (!empty($busqueda)) {
    // Contar total de resultados
    $sqlCount = "SELECT COUNT(*) as Total
            FROM cpcClientes c (NOLOCK)
            INNER JOIN genCreditos cr (NOLOCK) ON c.IdCliente = cr.IdCliente
            INNER JOIN genEstatusCredito e (NOLOCK) ON cr.IdEstatusCredito = e.IdEstatusCredito
            LEFT JOIN mscCardInfo msc (NOLOCK) ON c.IdCliente = msc.IdCliente
            LEFT JOIN genDocumentosDet r (NOLOCK) ON c.IdCliente = r.Referencia and IdDocumentoTipo in (2,3,11)
            WHERE cr.IdMoneda = 1
            AND (c.NombreCalculado LIKE ? OR c.IdCliente = ?)";

    $paramsCount = ['%' . $busqueda . '%', $busqueda];
    $stmtCount = sqlsrv_query($conn, $sqlCount, $paramsCount);

    if ($stmtCount !== false) {
        $rowCount = sqlsrv_fetch_array($stmtCount, SQLSRV_FETCH_ASSOC);
        $totalResultados = $rowCount['Total'];
        sqlsrv_free_stmt($stmtCount);
    }

    // Calcular offset
    $offset = ($paginaActual - 1) * $resultadosPorPagina;

    // Consulta con paginación
    $sql = "SELECT c.IdCliente,
                   c.NombreCalculado,
                   e.Nombre AS Estatus,
                   CASE WHEN msc.IdCliente IS NOT NULL THEN 'Sí' ELSE 'No' END AS EsMultiservice,
                   r.Nombre AS NombreDocumento,
                   r.RutaArchivo,
                   r.NombreArchivoFisico
            FROM cpcClientes c (NOLOCK)
            INNER JOIN genCreditos cr (NOLOCK) ON c.IdCliente = cr.IdCliente
            INNER JOIN genEstatusCredito e (NOLOCK) ON cr.IdEstatusCredito = e.IdEstatusCredito
            LEFT JOIN mscCardInfo msc (NOLOCK) ON c.IdCliente = msc.IdCliente
            LEFT JOIN genDocumentosDet r (NOLOCK) ON c.IdCliente = r.Referencia and IdDocumentoTipo in (2,3,11)
            WHERE cr.IdMoneda = 1
            AND (c.NombreCalculado LIKE ? OR c.IdCliente = ?)
            ORDER BY c.NombreCalculado
            OFFSET ? ROWS
            FETCH NEXT ? ROWS ONLY";

    $params = ['%' . $busqueda . '%', $busqueda, $offset, $resultadosPorPagina];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $mensaje = "Error en la consulta: " . print_r(sqlsrv_errors(), true);
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $resultados[] = $row;
        }

        if (empty($resultados) && $totalResultados == 0) {
            $mensaje = "No se encontraron resultados para: " . htmlspecialchars($busqueda);
        }

        sqlsrv_free_stmt($stmt);
    }
}

// Calcular total de páginas
$totalPaginas = $totalResultados > 0 ? ceil($totalResultados / $resultadosPorPagina) : 0;
?>

<style>
    .consulta-container {
        max-width: 100%;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 3px solid #e74c3c;
    }

    .section-header img {
        height: 60px;
        width: auto;
    }

    .section-header h2 {
        color: #2c3e50;
        font-size: 1.8em;
        margin: 0;
    }

    .search-container {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }

    .search-form {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-form label {
        font-weight: bold;
        color: white;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.1em;
    }

    .search-input {
        flex: 1;
        min-width: 300px;
        padding: 12px 15px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s;
    }

    .search-input:focus {
        outline: none;
        border-color: white;
        box-shadow: 0 0 15px rgba(255,255,255,0.3);
    }

    .search-btn {
        padding: 12px 30px;
        background: white;
        color: #f5576c;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .search-btn i {
        margin-right: 8px;
    }

    .mensaje {
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
        color: #856404;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-text {
        text-align: center;
        color: #7f8c8d;
        padding: 60px 40px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        font-size: 16px;
    }

    .info-text i {
        font-size: 64px;
        color: #bdc3c7;
        margin-bottom: 20px;
        display: block;
    }

    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: white;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 0.5px;
    }

    td {
        padding: 12px 15px;
        border-bottom: 1px solid #ecf0f1;
    }

    tbody tr {
        transition: background-color 0.2s;
    }

    tbody tr:hover {
        background-color: #f8f9fa;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-si {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .badge-no {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .btn-archivo {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-size: 12px;
        font-weight: bold;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .btn-archivo:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(231, 76, 60, 0.4);
    }

    .no-archivo {
        color: #95a5a6;
        font-style: italic;
        font-size: 13px;
    }

    .results-footer {
        text-align: center;
        padding: 15px;
        background: #ecf0f1;
        border-top: 2px solid #bdc3c7;
        color: #2c3e50;
        font-weight: bold;
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-top: 20px;
        gap: 10px;
        flex-wrap: wrap;
    }

    .pagination-info {
        color: #2c3e50;
        font-weight: bold;
        margin: 0 15px;
    }

    .pagination {
        display: flex;
        gap: 5px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        display: inline-block;
        padding: 8px 12px;
        border: 2px solid #f5576c;
        border-radius: 5px;
        text-decoration: none;
        color: #f5576c;
        font-weight: bold;
        transition: all 0.3s ease;
        min-width: 40px;
        text-align: center;
    }

    .pagination a:hover {
        background: #f5576c;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(245, 87, 108, 0.3);
    }

    .pagination .active {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-color: #f5576c;
    }

    .pagination .disabled {
        color: #bdc3c7;
        border-color: #bdc3c7;
        cursor: not-allowed;
        pointer-events: none;
    }

    .pagination .dots {
        border: none;
        color: #7f8c8d;
        cursor: default;
    }

    .pagination .dots:hover {
        background: transparent;
        transform: none;
        box-shadow: none;
    }

    @media (max-width: 768px) {
        .search-form {
            flex-direction: column;
            align-items: stretch;
        }

        .search-input {
            width: 100%;
            min-width: auto;
        }

        .search-btn {
            width: 100%;
        }

        table {
            font-size: 14px;
        }

        th, td {
            padding: 8px 10px;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="consulta-container">
    <div class="section-header">
        <img src="kwdaf.png" alt="Logo" class="logo">
        <h2><i class="fas fa-users"></i> Consulta de Firmas Autorizadas</h2>
    </div>

    <div class="search-container">
        <form method="POST" action="?seccion=consulta" class="search-form">
            <label for="busqueda">
                <i class="fas fa-search"></i> Buscar Cliente:
            </label>
            <input
                type="text"
                id="busqueda"
                name="busqueda"
                class="search-input"
                placeholder="Ingrese nombre o ID de cliente..."
                value="<?php echo htmlspecialchars($busqueda); ?>"
                required
                autofocus>
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i> Buscar
            </button>
        </form>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="mensaje">
            <i class="fas fa-exclamation-triangle"></i>
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($resultados) && empty($mensaje) && empty($busqueda)): ?>
        <div class="info-text">
            <i class="fas fa-info-circle"></i>
            <p>Ingresa un nombre o ID de cliente para comenzar la búsqueda</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($resultados)): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Cliente</th>
                        <th>Nombre</th>
                        <th>Estatus</th>
                        <th>Multiservice</th>
                        <th>Firma Autorizada</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $row): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['IdCliente']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['NombreCalculado']); ?></td>
                            <td><?php echo htmlspecialchars($row['Estatus']); ?></td>
                            <td>
                                <span class="badge <?php echo $row['EsMultiservice'] === 'Sí' ? 'badge-si' : 'badge-no'; ?>">
                                    <?php echo htmlspecialchars($row['EsMultiservice']); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                if (!empty($row['RutaArchivo']) && !empty($row['NombreArchivoFisico'])) {
                                    $rutaCompleta = $row['RutaArchivo'] . '/' . $row['NombreArchivoFisico'];
                                    $urlVisor = 'ver_documento.php?ruta=' . urlencode($row['RutaArchivo']) . '&archivo=' . urlencode($row['NombreArchivoFisico']);

                                    echo '<a href="' . htmlspecialchars($urlVisor) . '" target="_blank" class="btn-archivo" title="Ver archivo: ' . htmlspecialchars($row['NombreDocumento'] ?? 'Documento') . '">';
                                    echo '<i class="fas fa-file-pdf"></i> Ver Firma';
                                    echo '</a>';
                                } else {
                                    echo '<span class="no-archivo">Sin archivo</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="results-footer">
                <i class="fas fa-list"></i>
                Mostrando <?php echo count($resultados); ?> de <strong><?php echo $totalResultados; ?></strong> resultados
                (Página <?php echo $paginaActual; ?> de <?php echo $totalPaginas; ?>)
            </div>
        </div>

        <?php if ($totalPaginas > 1): ?>
            <div class="pagination-container">
                <div class="pagination-info">
                    Página <?php echo $paginaActual; ?> de <?php echo $totalPaginas; ?>
                </div>
                <ul class="pagination">
                    <?php if ($paginaActual > 1): ?>
                        <li>
                            <a href="?seccion=consulta&busqueda=<?php echo urlencode($busqueda); ?>&pagina=1" title="Primera página">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li><span class="disabled"><i class="fas fa-angle-double-left"></i></span></li>
                    <?php endif; ?>

                    <?php if ($paginaActual > 1): ?>
                        <li>
                            <a href="?seccion=consulta&busqueda=<?php echo urlencode($busqueda); ?>&pagina=<?php echo ($paginaActual - 1); ?>" title="Página anterior">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li><span class="disabled"><i class="fas fa-angle-left"></i></span></li>
                    <?php endif; ?>

                    <?php
                    $rango = 2;
                    $inicio = max(1, $paginaActual - $rango);
                    $fin = min($totalPaginas, $paginaActual + $rango);

                    if ($inicio > 1) {
                        echo '<li><a href="?seccion=consulta&busqueda=' . urlencode($busqueda) . '&pagina=1">1</a></li>';
                        if ($inicio > 2) {
                            echo '<li><span class="dots">...</span></li>';
                        }
                    }

                    for ($i = $inicio; $i <= $fin; $i++) {
                        if ($i == $paginaActual) {
                            echo '<li><span class="active">' . $i . '</span></li>';
                        } else {
                            echo '<li><a href="?seccion=consulta&busqueda=' . urlencode($busqueda) . '&pagina=' . $i . '">' . $i . '</a></li>';
                        }
                    }

                    if ($fin < $totalPaginas) {
                        if ($fin < $totalPaginas - 1) {
                            echo '<li><span class="dots">...</span></li>';
                        }
                        echo '<li><a href="?seccion=consulta&busqueda=' . urlencode($busqueda) . '&pagina=' . $totalPaginas . '">' . $totalPaginas . '</a></li>';
                    }
                    ?>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <li>
                            <a href="?seccion=consulta&busqueda=<?php echo urlencode($busqueda); ?>&pagina=<?php echo ($paginaActual + 1); ?>" title="Página siguiente">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li><span class="disabled"><i class="fas fa-angle-right"></i></span></li>
                    <?php endif; ?>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <li>
                            <a href="?seccion=consulta&busqueda=<?php echo urlencode($busqueda); ?>&pagina=<?php echo $totalPaginas; ?>" title="Última página">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li><span class="disabled"><i class="fas fa-angle-double-right"></i></span></li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
