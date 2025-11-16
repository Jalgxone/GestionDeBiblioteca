<h2>Crear Autor</h2>
<form method="post">
    <label>Nombre<br><input type="text" name="nombre" required value="<?php echo htmlspecialchars($nombre ?? ''); ?>"></label><br><br>
    <label>Nacionalidad<br><input type="text" name="nacionalidad" value="<?php echo htmlspecialchars($nacionalidad ?? ''); ?>"></label><br><br>
    <label>Fecha de nacimiento<br><input type="date" name="fecha_nacimiento" value="<?php echo htmlspecialchars($fecha_nacimiento ?? ''); ?>"></label><br><br>
    <button type="submit" class="btn">Crear</button>
</form>
