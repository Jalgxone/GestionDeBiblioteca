<h2>Relaciones Autores - Libros</h2>
<a href="?c=autorLibro&a=create" class="btn">Asignar autores a libro</a>
<table>
    <thead><tr><th>Libro</th><th>Autores</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach($libros as $l): ?>
        <tr>
            <td><?php echo htmlspecialchars($l['titulo']); ?></td>
            <td>
                <?php if(!empty($l['autores'])): ?>
                    <ul>
                        <?php foreach($l['autores'] as $a): ?>
                            <li>
                                <?php echo htmlspecialchars($a['nombre']); ?>
                                <a class="btn small ghost" href="?c=autor&a=edit&id=<?php echo $a['id']; ?>" style="margin-left:8px">Editar</a>
                                <a class="btn small danger" href="?c=autorLibro&a=remove&libro_id=<?php echo $l['id']; ?>&autor_id=<?php echo $a['id']; ?>" style="margin-left:8px" onclick="return confirm('¿Quitar relación autor-libro?');">Eliminar</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <em>Sin autores</em>
                <?php endif; ?>
            </td>
            <td>
                <a href="?c=autorLibro&a=create&libro_id=<?php echo $l['id']; ?>">Asignar autores</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
