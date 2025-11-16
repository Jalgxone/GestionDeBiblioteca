<h2>Asignar autores a libro</h2>
<form method="post">
    <label>Libro<br>
        <select name="libro_id" required>
            <option value="">-- Seleccione un libro --</option>
            <?php foreach($libros as $l): ?>
                <option value="<?php echo $l['id']; ?>" <?php if(!empty($_GET['libro_id']) && intval($_GET['libro_id'])==$l['id']) echo 'selected'; ?> ><?php echo htmlspecialchars($l['titulo']); ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <br><br>
    <label>Autores (Ctrl/Comando para seleccionar varios)<br>
        <select name="author_ids[]" multiple size="8">
            <?php foreach($autores as $a): ?>
                <option value="<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <br><br>
    <button type="submit">Asignar</button>
</form>
