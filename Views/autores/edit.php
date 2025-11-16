<h2>Editar Autor</h2>
<form method="post" action="?c=autor&a=update">
    <input type="hidden" name="id" value="<?php echo $autor['id']; ?>">
    <label>Nombre<br><input type="text" name="nombre" value="<?php echo htmlspecialchars($autor['nombre']); ?>" required></label><br><br>
    <label>Nacionalidad<br><input type="text" name="nacionalidad" value="<?php echo htmlspecialchars($autor['nacionalidad']); ?>"></label><br><br>
    <label>Fecha de nacimiento<br><input type="date" name="fecha_nacimiento" value="<?php echo htmlspecialchars($autor['fecha_nacimiento']); ?>"></label><br><br>
    <button type="submit" class="btn">Guardar cambios</button>
</form>