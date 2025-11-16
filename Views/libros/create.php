<h2>Crear Libro</h2>
<form method="post">
</form>
    <label>Título<br><input type="text" name="titulo" required value="<?php echo htmlspecialchars($titulo ?? ''); ?>"></label><br><br>
    <label>ISBN<br><input type="text" name="isbn" value="<?php echo htmlspecialchars($isbn ?? ''); ?>"></label><br><br>
    <label>Editorial<br><input type="text" name="editorial" value="<?php echo htmlspecialchars($editorial ?? ''); ?>"></label><br><br>
    <label>Fecha de publicación<br><input type="date" name="fecha_publicacion" value="<?php echo htmlspecialchars($fecha ?? ''); ?>"></label><br><br>
    <label>Cantidad total<br><input type="number" name="cantidad_total" value="<?php echo htmlspecialchars($cantidad ?? 1); ?>" min="1"></label><br><br>
    <label>Autores (Ctrl/Comando para seleccionar varios)<br>
        <select name="author_ids[]" multiple size="5">
            <?php foreach($autores as $a): ?>
                <option value="<?php echo $a['id']; ?>" <?php echo (!empty($author_ids) && in_array($a['id'], $author_ids)) ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <br><br>
    <button type="submit" class="btn">Crear libro</button>
    </form>
