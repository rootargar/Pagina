<?php
// Incluir archivo de conexión
include 'conexion.php';

// Función para calcular nuevo precio con margen
function calcularNuevoPrecio($costo, $porcentaje) {
    if ($porcentaje <= 9 || $porcentaje >= 100) {
        return $costo;
    }
    return $costo / (1 - ($porcentaje / 100));
}

// Función para mostrar resultados
function mostrarResultados($conn, $query, $params, $nuevosPrecios) {
    $result = sqlsrv_query($conn, $query, $params);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($result)) {
        echo "<form method='POST' action='' id='formularioPrincipal'>";
        echo "<input type='hidden' name='id_almacen' value='" . htmlspecialchars($_GET['id_almacen'] ?? '') . "'>";
        echo "<input type='hidden' name='num_parte' value='" . htmlspecialchars($_GET['num_parte'] ?? '') . "'>";
        echo "<input type='hidden' name='descripcion' value='" . htmlspecialchars($_GET['descripcion'] ?? '') . "'>";
        echo "<table>";
        echo "<tr><th>Parte</th><th>Nombre</th><th>Existencia</th><th>Categoría</th><th>Precio Mínimo</th><th>% Categoría</th><th>% Usuario</th><th>Nuevo Precio</th></tr>";

while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $id = $row['IdArticulo'];
    $costo = $row['Costo'];
    $precioMinimo = $row['PrecioMinimo'];
    $nuevoPrecio = $nuevosPrecios[$id] ?? '';

    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['NumArticulo']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Existencia']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Categoria']) . "</td>";
    echo "<td>$" . number_format($row['PrecioMinimo'], 2) . "</td>";
    echo "<td>" . htmlspecialchars($row['PorcMargenUtilMin']) . "%</td>";
    echo "<td>
        <input type='hidden' name='costo[$id]' value='$costo' class='costo-hidden'>
        <input type='hidden' name='precio_minimo[$id]' value='$precioMinimo' class='precio-minimo-hidden'>
        <input type='number'
               name='porcentaje[$id]'
               placeholder='%'
               step='0.01'
               min='10'
               max='80'
               value='" . htmlspecialchars($_POST['porcentaje'][$id] ?? '') . "'
               class='porcentaje-input'
               data-costo='$costo'
               data-precio-minimo='$precioMinimo'
               data-id='$id'
               onblur='calcularPrecioAutomatico(this)'>
    </td>";

    // Determinar el color basado en la comparación con precio mínimo
    $colorClass = '';
    $textoAdicional = '';
    if ($nuevoPrecio) {
        if ($nuevoPrecio < $precioMinimo) {
            $colorClass = 'precio-bajo-minimo';
            $textoAdicional = ' (Precio bajo)';
        } else {
            $colorClass = 'precio-sobre-minimo';
        }
    }

    echo "<td><span id='precio_$id' class='precio-resultado $colorClass'>" .
         ($nuevoPrecio ? "$" . number_format($nuevoPrecio, 2) . $textoAdicional : "") .
         "</span></td>";
    echo "</tr>";
}
 echo "</table>";
        echo "</form>";
    } else {
        echo "<p class='message error'><i class='fas fa-exclamation-triangle'></i> No se encontraron resultados para los criterios especificados.</p>";
    }

    sqlsrv_free_stmt($result);
}

// Procesar cálculo de precios
$nuevosPrecios = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calcular_precio'])) {
    foreach ($_POST['costo'] as $id => $costo) {
        $porcentaje = $_POST['porcentaje'][$id] ?? 0;
        if ($porcentaje > 0) {
            $nuevosPrecios[$id] = calcularNuevoPrecio($costo, $porcentaje);
        }
    }
}
?>

<style>
    .cotizador-container {
        max-width: 100%;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 3px solid #3498db;
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

    .almacen-selector {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 25px;
        color: white;
    }

    .almacen-selector h3 {
        color: white;
        margin-bottom: 15px;
    }

    .select-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
        max-width: 350px;
    }

    select {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 8px;
        font-size: 16px;
        background-color: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    select:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 10px rgba(52, 152, 219, 0.3);
    }

    .almacen-info {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .almacen-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.1em;
    }

    .search-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .search-form {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 2px solid #f0f0f0;
        transition: all 0.3s ease;
    }

    .search-form:hover {
        border-color: #3498db;
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.15);
    }

    .search-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #2c3e50;
        font-size: 1em;
    }

    .search-form input[type="text"] {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .search-form input[type="text"]:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 10px rgba(52, 152, 219, 0.2);
    }

    .button {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.3s ease;
        width: 100%;
    }

    .button:hover {
        background: linear-gradient(135deg, #2980b9, #1f4e79);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    #resultados {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    #resultados h2 {
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    th {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.85em;
        letter-spacing: 0.5px;
    }

    td {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    tr:hover {
        background-color: #f8f9fa;
    }

    tr:last-child td {
        border-bottom: none;
    }

    input[type="number"] {
        width: 90px;
        padding: 8px;
        border: 2px solid #ddd;
        border-radius: 6px;
        text-align: center;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    input[type="number"]:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
    }

    .precio-resultado {
        font-weight: bold;
        font-size: 1em;
        padding: 5px 10px;
        border-radius: 6px;
        display: inline-block;
    }

    .precio-bajo-minimo {
        color: #e74c3c;
        background-color: #ffebee;
        border: 1px solid #ffcdd2;
        animation: pulseRed 1s ease-in-out;
    }

    .precio-sobre-minimo {
        color: #27ae60;
        background-color: #e8f5e8;
        border: 1px solid #c8e6c9;
    }

    @keyframes pulseRed {
        0%, 100% { background-color: #ffebee; }
        50% { background-color: #ffcdd2; }
    }

    .calculando {
        background-color: #fff3cd;
        border-color: #ffeaa7;
    }

    .message {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .error {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .info {
        background-color: #d1ecf1;
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }

    @media (max-width: 768px) {
        .search-container {
            grid-template-columns: 1fr;
        }

        table {
            font-size: 12px;
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        th, td {
            padding: 8px 10px;
        }

        input[type="number"] {
            width: 70px;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="cotizador-container">
    <div class="section-header">
        <img src="kwdaf.png" alt="Logo" class="logo">
        <h2><i class="fas fa-calculator"></i> Cotizador de Refacciones</h2>
    </div>

    <!-- Selector de almacén -->
    <div class="almacen-selector">
        <h3><i class="fas fa-warehouse"></i> Seleccione Sucursal:</h3>
        <div class="select-wrapper">
            <select id="almacenSelect" onchange="seleccionarAlmacen()">
                <option value="0">Seleccione un almacén...</option>
                <option value="1" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1) echo 'selected'; ?>>Matriz</option>
                <option value="2" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 2) echo 'selected'; ?>>Mazatlán</option>
                <option value="3" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 3) echo 'selected'; ?>>Mochis</option>
                <option value="4" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 4) echo 'selected'; ?>>Guasave</option>
                <option value="13" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 13) echo 'selected'; ?>>Taller Servicio</option>
                <option value="1209" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1209) echo 'selected'; ?>>Taller Inn House Casa Ley</option>
                <option value="1215" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1215) echo 'selected'; ?>>Guamuchil</option>
                <option value="1216" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1216) echo 'selected'; ?>>TRP Mazatlán</option>
                <option value="16" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 16) echo 'selected'; ?>>Exhibicion Matriz</option>
                <option value="104" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 104) echo 'selected'; ?>>Exhibicion Mazatlan</option>
                <option value="1183" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1183) echo 'selected'; ?>>Exhibicion Mochis</option>
                <option value="1199" <?php if (isset($_GET['id_almacen']) && $_GET['id_almacen'] == 1199) echo 'selected'; ?>>Exhibicion Guasave</option>
            </select>
        </div>
    </div>

    <!-- Información del almacén seleccionado -->
    <div id="almacenInfo" class="almacen-info" style="display: <?php echo isset($_GET['id_almacen']) && $_GET['id_almacen'] != '0' ? 'block' : 'none'; ?>">
        <div class="almacen-badge">
            <i class="fas fa-warehouse"></i>
            <span>Almacén actual: <strong><?php
                if(isset($_GET['id_almacen'])) {
                    $almacen_id = $_GET['id_almacen'];
                    $almacenes = array(
                        '1' => 'KWS Matriz',
                        '2' => 'KWS Mazatlán',
                        '3' => 'KWS Mochis',
                        '4' => 'KWS Guasave',
                        '13' => 'Consignación de Taller Servicio',
                        '1209' => 'Consignación Taller Inn House Casa Ley',
                        '1215' => 'KWS Guamuchil',
                        '1216' => 'KWS TRP Mazatlán',
                        '16' => 'Exhibicion Matriz',
                        '104' => 'Exhibicion Mazatlan',
                        '1183' => 'Exhibicion Mochis',
                        '1199' => 'Exhibicion Guasave'
                    );
                    echo isset($almacenes[$almacen_id]) ? htmlspecialchars($almacenes[$almacen_id]) : 'Almacén no encontrado';
                }
            ?></strong></span>
        </div>
    </div>

    <!-- Formularios de búsqueda -->
    <div class="search-container">
        <form method="GET" action="" class="search-form">
            <input type="hidden" name="seccion" value="cotizador">
            <input type="hidden" name="id_almacen" value="<?php echo isset($_GET['id_almacen']) ? htmlspecialchars($_GET['id_almacen']) : ''; ?>">
            <label for="num_parte"><i class="fas fa-hashtag"></i> Número de Parte:</label>
            <input type="text" id="num_parte" name="num_parte" placeholder="Ingrese el número de parte" value="<?php echo isset($_GET['num_parte']) ? htmlspecialchars($_GET['num_parte']) : ''; ?>">
            <button class="button" name="buscar_parte"><i class="fas fa-search"></i> Buscar por Parte</button>
        </form>

        <form method="GET" action="" class="search-form">
            <input type="hidden" name="seccion" value="cotizador">
            <input type="hidden" name="id_almacen" value="<?php echo isset($_GET['id_almacen']) ? htmlspecialchars($_GET['id_almacen']) : ''; ?>">
            <label for="descripcion"><i class="fas fa-align-left"></i> Descripción:</label>
            <input type="text" id="descripcion" name="descripcion" placeholder="Ingrese la descripción" value="<?php echo isset($_GET['descripcion']) ? htmlspecialchars($_GET['descripcion']) : ''; ?>">
            <button class="button" name="buscar_descripcion"><i class="fas fa-search"></i> Buscar por Descripción</button>
        </form>
    </div>

    <!-- Resultados -->
    <section id="resultados">
        <h2><i class="fas fa-list-alt"></i> Resultados de Búsqueda</h2>

        <?php if (!empty($nuevosPrecios)): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                Precios calculados exitosamente con los márgenes especificados.
            </div>
        <?php endif; ?>

        <?php
        // Validar que se haya seleccionado un almacén
        if (!isset($_GET['id_almacen']) || $_GET['id_almacen'] == '0') {
            echo "<div class='message info'><i class='fas fa-info-circle'></i> Por favor seleccione un almacén para realizar búsquedas.</div>";
        } else {
            // Búsqueda por número de parte
            if (isset($_GET['buscar_parte']) && !empty($_GET['num_parte'])) {
                $num_parte = $_GET['num_parte'];
                $id_almacen = $_GET['id_almacen'];

                $query = "SELECT a.IdArticulo, a.NumArticulo, a.Nombre, e.ExistenciaActual as Existencia,
                        ct.Nombre as Categoria, ct.PorcMargenUtilMin,
                        ((c.Costo / (1 - (ct.PorcMargenUtilMin / 100))) - c.Costo + c.Costo) as PrecioMinimo,
                        c.Costo
                FROM InvArticulos a
                INNER JOIN invArticulosCostos c ON a.IdArticulo = c.IdArticulo
                INNER JOIN invArticulosCat ct ON a.IdArticuloCat = ct.IdArticuloCat
                INNER JOIN InvArticulosExistencias e ON a.IdArticulo = e.IdArticulo
                WHERE c.IdAlmacen = ? AND e.IdAlmacen = ?
                AND a.IdTipoInventario = 1
                AND a.Activo = 1
                AND e.ExistenciaActual > 0
                AND c.ExistenciaActual > 0
                AND c.FechaUltimaEntrada = (SELECT MAX(cs.FechaUltimaEntrada) FROM invArticulosCostos cs WHERE cs.IdArticulo = c.IdArticulo AND cs.ExistenciaActual > 0 AND cs.IdAlmacen = ?)
                AND a.NumArticulo LIKE ?";

                $params = array($id_almacen, $id_almacen, $id_almacen, "%$num_parte%");
                mostrarResultados($conn, $query, $params, $nuevosPrecios);
            }

            // Búsqueda por descripción
            elseif (isset($_GET['buscar_descripcion']) && !empty($_GET['descripcion'])) {
                $descripcion = $_GET['descripcion'];
                $id_almacen = $_GET['id_almacen'];

                $query = "SELECT a.IdArticulo, a.NumArticulo, a.Nombre, e.ExistenciaActual as Existencia,
                        ct.Nombre as Categoria, ct.PorcMargenUtilMin,
                        ((c.Costo / (1 - (ct.PorcMargenUtilMin / 100))) - c.Costo + c.Costo) as PrecioMinimo,
                        c.Costo
                FROM InvArticulos a
                INNER JOIN invArticulosCostos c ON a.IdArticulo = c.IdArticulo
                INNER JOIN invArticulosCat ct ON a.IdArticuloCat = ct.IdArticuloCat
                INNER JOIN InvArticulosExistencias e ON a.IdArticulo = e.IdArticulo
                WHERE c.IdAlmacen = ? AND e.IdAlmacen = ?
                AND a.IdTipoInventario = 1
                AND a.Activo = 1
                AND e.ExistenciaActual > 0
                AND c.ExistenciaActual > 0
                AND c.FechaUltimaEntrada = (SELECT MAX(cs.FechaUltimaEntrada) FROM invArticulosCostos cs WHERE cs.IdArticulo = c.IdArticulo AND cs.ExistenciaActual > 0 AND cs.IdAlmacen = ?)
                AND a.Nombre LIKE ?";

                $params = array($id_almacen, $id_almacen, $id_almacen, "%$descripcion%");
                mostrarResultados($conn, $query, $params, $nuevosPrecios);
            }

            // Si se calcularon precios, volver a mostrar los resultados
            elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calcular_precio'])) {
                if (!empty($_POST['num_parte'])) {
                    $num_parte = $_POST['num_parte'];
                    $id_almacen = $_POST['id_almacen'];

                    $query = "SELECT a.IdArticulo, a.NumArticulo, a.Nombre, e.ExistenciaActual as Existencia,
                            ct.Nombre as Categoria, ct.PorcMargenUtilMin,
                            ((c.Costo / (1 - (ct.PorcMargenUtilMin / 100))) - c.Costo + c.Costo) as PrecioMinimo,
                            c.Costo
                    FROM InvArticulos a
                    INNER JOIN invArticulosCostos c ON a.IdArticulo = c.IdArticulo
                    INNER JOIN invArticulosCat ct ON a.IdArticuloCat = ct.IdArticuloCat
                    INNER JOIN InvArticulosExistencias e ON a.IdArticulo = e.IdArticulo
                    WHERE c.IdAlmacen = ? AND e.IdAlmacen = ?
                    AND a.IdTipoInventario = 1
                    AND a.Activo = 1
                    AND e.ExistenciaActual > 0
                    AND c.ExistenciaActual > 0
                    AND c.FechaUltimaEntrada = (SELECT MAX(cs.FechaUltimaEntrada) FROM invArticulosCostos cs WHERE cs.IdArticulo = c.IdArticulo AND cs.ExistenciaActual > 0 AND cs.IdAlmacen = ?)
                    AND a.NumArticulo LIKE ?";

                    $params = array($id_almacen, $id_almacen, $id_almacen, "%$num_parte%");
                    mostrarResultados($conn, $query, $params, $nuevosPrecios);
                } elseif (!empty($_POST['descripcion'])) {
                    $descripcion = $_POST['descripcion'];
                    $id_almacen = $_POST['id_almacen'];

                    $query = "SELECT a.IdArticulo, a.NumArticulo, a.Nombre, e.ExistenciaActual as Existencia,
                            ct.Nombre as Categoria, ct.PorcMargenUtilMin,
                            ((c.Costo / (1 - (ct.PorcMargenUtilMin / 100))) - c.Costo + c.Costo) as PrecioMinimo,
                            c.Costo
                    FROM InvArticulos a
                    INNER JOIN invArticulosCostos c ON a.IdArticulo = c.IdArticulo
                    INNER JOIN invArticulosCat ct ON a.IdArticuloCat = ct.IdArticuloCat
                    INNER JOIN InvArticulosExistencias e ON a.IdArticulo = e.IdArticulo
                    WHERE c.IdAlmacen = ? AND e.IdAlmacen = ?
                    AND a.IdTipoInventario = 1
                    AND a.Activo = 1
                    AND e.ExistenciaActual > 0
                    AND c.ExistenciaActual > 0
                    AND c.FechaUltimaEntrada = (SELECT MAX(cs.FechaUltimaEntrada) FROM invArticulosCostos cs WHERE cs.IdArticulo = c.IdArticulo AND cs.ExistenciaActual > 0 AND cs.IdAlmacen = ?)
                    AND a.Nombre LIKE ?";

                    $params = array($id_almacen, $id_almacen, $id_almacen, "%$descripcion%");
                    mostrarResultados($conn, $query, $params, $nuevosPrecios);
                }
            } else {
                echo "<div class='message info'><i class='fas fa-search'></i> Utilice uno de los formularios de búsqueda para encontrar refacciones.</div>";
            }
        }
        ?>
    </section>
</div>

<script>
    function seleccionarAlmacen() {
        const select = document.getElementById('almacenSelect');
        const idAlmacen = select.value;

        if (idAlmacen != '0') {
            document.getElementById('almacenInfo').style.display = 'block';

            const url = new URL(window.location.href);
            url.searchParams.set('seccion', 'cotizador');
            url.searchParams.set('id_almacen', idAlmacen);
            window.location.href = url;
        } else {
            document.getElementById('almacenInfo').style.display = 'none';
        }
    }

    function calcularPrecioAutomatico(input) {
        const costo = parseFloat(input.getAttribute('data-costo'));
        const precioMinimo = parseFloat(input.getAttribute('data-precio-minimo'));
        const porcentaje = parseFloat(input.value);
        const id = input.getAttribute('data-id');
        const precioSpan = document.getElementById('precio_' + id);

        input.classList.add('calculando');

        setTimeout(() => {
            input.classList.remove('calculando');

            if (porcentaje > 9 && porcentaje < 100 && !isNaN(costo) && !isNaN(porcentaje)) {
                const nuevoPrecio = costo / (1 - (porcentaje / 100));

                const precioFormateado = '$' + nuevoPrecio.toLocaleString('es-MX', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                precioSpan.className = 'precio-resultado';

                if (nuevoPrecio < precioMinimo) {
                    precioSpan.className += ' precio-bajo-minimo';
                    precioSpan.textContent = precioFormateado + ' -MVMC';
                } else {
                    precioSpan.className += ' precio-sobre-minimo';
                    precioSpan.textContent = precioFormateado;
                }

            } else {
                precioSpan.textContent = '';
                precioSpan.className = 'precio-resultado';

                if (porcentaje <= 9 || porcentaje >= 100) {
                    precioSpan.textContent = 'Porcentaje inválido';
                    precioSpan.style.color = '#e74c3c';
                }
            }
        }, 200);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.search-form');

        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const idAlmacen = document.getElementById('almacenSelect').value;

                if (idAlmacen == '0') {
                    e.preventDefault();
                    alert('Por favor seleccione un almacén antes de realizar la búsqueda.');
                    return false;
                }

                const input = this.querySelector('input[type="text"]');
                if (!input.value.trim()) {
                    e.preventDefault();
                    alert('Por favor ingrese un criterio de búsqueda.');
                    return false;
                }
            });
        });

        document.addEventListener('keyup', function(e) {
            if (e.target.classList.contains('porcentaje-input')) {
                clearTimeout(e.target.timeout);
                e.target.timeout = setTimeout(() => {
                    calcularPrecioAutomatico(e.target);
                }, 300);
            }
        });
    });
</script>
