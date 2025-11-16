<h2>Editar Cliente</h2>
<form method="post" action="?c=cliente&a=update">
    <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
    <label>Nombre<br><input type="text" name="nombre" required value="<?php echo htmlspecialchars($cliente['nombre']); ?>"></label><br><br>
    <label>Email<br><input type="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>"></label><br><br>
    <label>Teléfono<br><input type="text" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>"></label><br><br>
    <label>Documento<br><input type="text" name="documento" value="<?php echo htmlspecialchars($cliente['documento']); ?>"></label><br><br>
    <button type="submit">Guardar</button>
</form>
