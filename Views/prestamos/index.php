<h2>Préstamos</h2>
<a class="btn" href="?c=prestamo&a=create">Crear préstamo</a>
<table>
    <thead><tr><th>ID</th><th>Libro</th><th>Cliente/Usuario</th><th>Fecha préstamo</th><th>Devuelto</th><th>Acción</th></tr></thead>
    <tbody>
    <?php foreach($prestamos as $p): ?>
        <tr>
            <td><?php echo $p['id']; ?></td>
            <td><?php echo htmlspecialchars($p['titulo']); ?></td>
            <td><?php echo htmlspecialchars($p['cliente_nombre'] ?? $p['usuario_nombre'] ?? ''); ?></td>
            <td><?php echo $p['fecha_prestamo']; ?></td>
            <td><?php echo $p['devuelto'] ? 'Sí' : 'No'; ?></td>
            <td>
                <?php if(!$p['devuelto']): ?>
                    <a class="btn small ghost" href="?c=prestamo&a=devolver&id=<?php echo $p['id']; ?>">Marcar devolución</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
