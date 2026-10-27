# Adoquines Milkito

Sitio de delivery de helados tipo adoquín para Huánuco, desarrollado en PHP + MySQL para ejecutarse en XAMPP y alojarse luego en InfinityFree.

## Instalación en XAMPP

1. Inicia Apache y MySQL desde el panel de XAMPP.
2. Abre `http://localhost/phpmyadmin` y ejecuta el contenido de `database.sql`.
3. Verifica las credenciales de MySQL en `config.php` (por defecto XAMPP usa usuario `root` sin contraseña).
4. Abre `http://localhost/Adoquines%20Milkito/`.
5. Panel de pedidos: `http://localhost/Adoquines%20Milkito/admin.php`. Contraseña inicial: `milkito2026`.

## Publicación en InfinityFree

Sube todos los archivos a `htdocs` mediante el administrador de archivos o FTP. Crea una base MySQL desde el panel de InfinityFree, importa `database.sql` y reemplaza en `config.php` `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS` con los datos proporcionados por el hosting. Cambia también `ADMIN_PASSWORD` antes de publicar.

## Incluye

- Catálogo de sabores editable desde la tabla `flavors`.
- Personalización por tamaño, sabor y cantidad.
- Registro de cliente, celular, dirección, distrito y notas.
- Panel privado para consultar pedidos y actualizar su estado.
- Protección CSRF en formularios y consultas preparadas con PDO.
