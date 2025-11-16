<h2>Autores</h2>
<a class="btn" href="?c=autor&a=create">Crear autor</a>
<table>
    <thead><tr><th>ID</th><th>Nombre</th><th>Nacionalidad</th><th>F. Nac.</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach($autores as $a): ?>
        <tr>
            <td><?php echo $a['id']; ?></td>
            <td><?php echo htmlspecialchars($a['nombre']); ?></td>
            <td><?php echo htmlspecialchars($a['nacionalidad']); ?></td>
            <td><?php echo $a['fecha_nacimiento']; ?></td>
            <td>
                <a class="btn small ghost" href="?c=autor&a=edit&id=<?php echo $a['id']; ?>">Editar</a>
                <a class="btn small danger" href="?c=autor&a=delete&id=<?php echo $a['id']; ?>" onclick="return confirm('¿Eliminar autor?');">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
