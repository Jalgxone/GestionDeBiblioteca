<h2>Clientes</h2>
<a href="?c=cliente&a=create" class="btn">Crear cliente</a>
<table>
    <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach($clientes as $c): ?>
        <tr>
            <td><?php echo $c['id']; ?></td>
            <td><?php echo htmlspecialchars($c['nombre']); ?></td>
            <td><?php echo htmlspecialchars($c['email']); ?></td>
            <td><?php echo htmlspecialchars($c['telefono']); ?></td>
            <td>
                <a class="btn small ghost" href="?c=cliente&a=edit&id=<?php echo $c['id']; ?>">Editar</a>
                <a class="btn small danger" href="?c=cliente&a=delete&id=<?php echo $c['id']; ?>" onclick="return confirm('¿Eliminar cliente?');">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
