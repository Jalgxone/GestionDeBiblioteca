<h2>Editar Libro</h2>
<form method="post" action="?c=libro&a=update">
    <input type="hidden" name="id" value="<?php echo $libro['id']; ?>">
    <label>Título<br><input type="text" name="titulo" required value="<?php echo htmlspecialchars($libro['titulo']); ?>"></label><br><br>
    <label>ISBN<br><input type="text" name="isbn" value="<?php echo htmlspecialchars($libro['isbn']); ?>"></label><br><br>
    <label>Editorial<br><input type="text" name="editorial" value="<?php echo htmlspecialchars($libro['editorial']); ?>"></label><br><br>
    <label>Fecha de publicación<br><input type="date" name="fecha_publicacion" value="<?php echo $libro['fecha_publicacion']; ?>"></label><br><br>
    <label>Cantidad total<br><input type="number" name="cantidad_total" min="1" value="<?php echo $libro['cantidad_total']; ?>"></label><br><br>
    <label>Autores (Ctrl/Comando para seleccionar varios)<br>
        <select name="author_ids[]" multiple size="6">
            <?php foreach($autores as $a): ?>
                <option value="<?php echo $a['id']; ?>" <?php echo in_array($a['id'],$selected) ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <br><br>
    <button type="submit">Guardar cambios</button>
</form>
