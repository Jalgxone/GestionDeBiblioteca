<h2>Libros</h2>
<a class="btn" href="?c=libro&a=create">Crear libro</a>
<table>
    <thead><tr><th>ID</th><th>Título</th><th>ISBN</th><th>Editorial</th><th>Fecha publicación</th><th>Disponible</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach($libros as $l): ?>
        <tr>
            <td><?php echo $l['id']; ?></td>
            <td><?php echo htmlspecialchars($l['titulo']); ?></td>
            <td><?php echo htmlspecialchars($l['isbn']); ?></td>
            <td><?php echo htmlspecialchars($l['editorial']); ?></td>
            <td><?php echo htmlspecialchars($l['fecha_publicacion'] ?? ''); ?></td>
            <td><?php echo $l['cantidad_disponible']; ?></td>
            <td>
                <a class="btn small ghost" href="?c=libro&a=edit&id=<?php echo $l['id']; ?>">Editar</a>
                <a class="btn small danger" href="?c=libro&a=delete&id=<?php echo $l['id']; ?>" onclick="return confirm('¿Eliminar libro? Esta acción no se puede deshacer.');">Eliminar</a>
            </td>
        </tr>
        <tr>
            <td colspan="7"><strong>Autores:</strong>
                <?php if(!empty($l['autores'])): ?>
                    <?php echo implode(', ', array_map(function($a){ return $a['nombre']; }, $l['autores'])); ?>
                <?php else: ?>
                    <em>Sin autores asignados</em>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
