<?php
require_once __DIR__ . '/../queries/clientes.queries.php';

$filtros = [
    'estado' => $_GET['estado'] ?? '',
    'buscar' => trim($_GET['buscar'] ?? ''),
    'desde'  => $_GET['desde'] ?? '',
    'hasta'  => $_GET['hasta'] ?? '',
];
$page     = max(1, (int)($_GET['p'] ?? 1));
$per_page = 20;
$offset   = ($page - 1) * $per_page;

$total_filtrado = q_clientes_count($pdo, $filtros);
$total_pages    = max(1, ceil($total_filtrado / $per_page));
$clientes       = q_clientes_lista($pdo, $filtros, $per_page, $offset);

// Estados del enum real de la DB tabla clientes
$estados = ['lead', 'activo', 'inactivo'];
$hay_filtro = array_filter($filtros);
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;flex-wrap:wrap;gap:10px">
    <div class="section-title">Gestión de clientes</div>
    <a href="/export/clientes-csv.php<?= $hay_filtro ? '?'.http_build_query(array_filter($filtros)) : '' ?>"
       class="btn btn-ghost" style="font-size:0.78rem">↓ Exportar CSV</a>
</div>
<div class="section-sub">
    <?= number_format($total_filtrado) ?> clientes
    <?= $hay_filtro ? '<span style="color:var(--green)">· filtro activo</span>' : 'en total' ?>
</div>

<form method="GET" class="filters">
    <input type="hidden" name="action" value="clientes">
    <input type="text" name="buscar" placeholder="Nombre, email o teléfono…"
           value="<?= htmlspecialchars($filtros['buscar']) ?>" style="width:250px">
    <select name="estado">
        <option value="">Todos los estados</option>
        <?php foreach ($estados as $e): ?>
        <option value="<?= $e ?>" <?= $filtros['estado']===$e ? 'selected' : '' ?>><?= ucfirst($e) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="desde" value="<?= htmlspecialchars($filtros['desde']) ?>" style="width:136px">
    <input type="date" name="hasta" value="<?= htmlspecialchars($filtros['hasta']) ?>" style="width:136px">
    <button type="submit" class="btn btn-primary">Filtrar</button>
    <a href="?action=clientes" class="btn btn-ghost">Limpiar</a>
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Alta</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($clientes as $c): ?>
                <tr onclick="window.location='?action=cliente&id=<?= $c['id'] ?>'" style="cursor:pointer">
                    <td><span class="id-display">#<?= $c['id'] ?></span></td>
                    <td><?= htmlspecialchars($c['nombre_cliente'] ?: '—') ?></td>
                    <td><?= htmlspecialchars($c['email_cliente'] ?: '—') ?></td>
                    <td class="mono"><?= htmlspecialchars($c['telefono_cliente'] ?: '—') ?></td>
                    <td>
                        <span class="badge badge-<?= htmlspecialchars($c['estado']) ?>">
                            <?= ucfirst($c['estado']) ?>
                        </span>
                    </td>
                    <td><?= $c['fecha_alta'] ? date('d/m/y', strtotime($c['fecha_alta'])) : '—' ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$clientes): ?>
            <tr><td colspan="6" style="text-align:center;padding:48px;color:var(--text3)">No se encontraron registros</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($total_pages > 1):
    $base = '?action=clientes'
        . '&estado=' . urlencode($filtros['estado'])
        . '&buscar=' . urlencode($filtros['buscar'])
        . '&desde='  . urlencode($filtros['desde'])
        . '&hasta='  . urlencode($filtros['hasta']);
?>
<div class="pagination">
    <span class="meta"><?= $total_filtrado ?> · pág <?= $page ?>/<?= $total_pages ?></span>
    <?php if ($page > 1): ?><a href="<?= $base ?>&p=<?= $page-1 ?>">← Ant.</a><?php endif; ?>
    <?php for ($i = max(1,$page-2); $i <= min($total_pages,$page+2); $i++):
        echo "<a href='$base&p=$i' class='".($i===$page?'active':'')."'>$i</a>";
    endfor; ?>
    <?php if ($page < $total_pages): ?><a href="<?= $base ?>&p=<?= $page+1 ?>">Sig. →</a><?php endif; ?>
</div>
<?php endif; ?>
