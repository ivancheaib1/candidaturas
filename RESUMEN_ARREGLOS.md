# Resumen Completo de Arreglos - Sistema de Votación

## Fecha: 7-8 de Mayo 2026

---

## 📋 Problemas Solucionados

### 1. **Conexión a Base de Datos (Docker)**
**Problema:** 
- MySQL estaba en puerto 3306, pero docker-compose.yml y conexion.php usaban puerto 3310
- Error: `Network is unreachable` cuando intentaba conectar a `host.docker.internal:3310`

**Solución:** 
- Actualizar puerto de 3310 → 3306 en docker-compose.yml y conexion.php
- Función `construirURL()` para detectar automáticamente puerto en local y producción

**Archivos modificados:**
- `docker-compose.yml`
- `ws/conexion.php`
- `ws/funciones.php` (agregada función `construirURL()`)

---

### 2. **Error en carga_datos.php**
**Problema:** 
```
Fatal error: array_values(): Argument #1 ($array) must be of type array, null given
```
- Variable `$url` no definida (código comentado)

**Solución:** 
- Descomentar código correcto que define `$url`
- Eliminar uso innecesario de `array_values()`

**Archivo modificado:** `carga_datos.php` línea 4-6

---

### 3. **Registro de Votos No Funciona (Rol Veedor)**
**Problema:**
- El voto no se registraba cuando se ingresaba mesa + orden
- Redireccionamiento manual no funcionaba en Docker/Producción

**Causas identificadas:**
- Sin validación si consulta SELECT retorna resultados
- `header()` con URL hardcodeada (`http://localhost`) no funcionaba en Docker
- Los parámetros no se escapaban correctamente

**Solución:**
- Validar que consulta retorne datos ANTES de usarlos
- Convertir mesa y orden a enteros (prevenir SQL injection)
- Usar función `construirURL()` que detecta automáticamente:
  - Protocolo (http/https)
  - Host con puerto (localhost:8080 en Docker, votacion.com.py en producción)
  - Parámetros correctamente formateados

**Archivo modificado:** `actualizavoto.php`

```php
// ANTES (no funcionaba)
header("Location: http://".$_SERVER["SERVER_NAME"]."/".$carpeta_sistema."carga_datos.php?status=ok&mesa=".$mesa."&orden=".$orden);

// DESPUÉS (funciona en todo ambiente)
header("Location: " . construirURL("carga_datos.php", array("status" => "ok", "mesa" => $mesa, "orden" => $orden)));
```

---

### 4. **Botón Excel Visible para Todos los Roles**
**Problema:**
- Usuarios Editor y Veedor veían botón de descargar Excel (solo Admin debe verlo)

**Solución:**
- Ocultar botón solo para Admin (`nivel_acceso == 0`)

**Archivo modificado:** `inicio.php` línea 82-88

```php
<?php if(isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel_acceso'] == 0){ ?>
<button type="button" class="btn btn-warning col-sm-2  col-sx-4 " style="display:none" id="btndescargar" onclick="descargar('descargarPersonas')">
    <i class="fa fa-file-excel"></i> EXCEL 
</button>
<?php } ?>
```

---

### 5. **Error del Web Service - No se pudo determinar el servicio web**
**Problema:**
- Error 404 en `/ws/ws.php` 
- Mensaje: "Error al conectar con el WS inutil"
- Ocurría en roles **Editor** y **Veedor**

**Causas identificadas:**
- Variable `$wsUrl` no definida en JavaScript
- Se construía URL incompleta: `ws/ws.php` (vacía)
- Faltaba validación antes de hacer petición AJAX

**Solución:**

#### **Para rol Editor (en `index.php`):**
```javascript
// Después de definir $wsUrl en PHP, pasarlo a JavaScript
var $wsUrl = '<?php echo $wsUrl; ?>';
```

#### **Para rol Veedor (en `carga_datos.php`):**
```javascript
var $wsUrl = 'Padron';
```

#### **Validación en funciones.js:**
- Agregar validación para no llamar WS si `$wsUrl` está vacío
- Mostrar mensaje de error claro si no se puede determinar el servicio

**Archivos modificados:**
- `index.php` (agregar script de `$wsUrl`)
- `carga_datos.php` (agregar script de `$wsUrl` y función `buscarRegistro()`)
- `cargar_datos1.php` (agregar script de `$wsUrl`)
- `js/funciones.js` (agregar validación)

---

### 6. **Función buscarRegistro() No Definida (Veedor)**
**Problema:**
- Botón ENVIAR en Veedor llamaba a `buscarRegistro()` que no existía

**Solución:**
- Agregar función `buscarRegistro()` en `carga_datos.php` que envía el formulario

```javascript
function buscarRegistro(){
    document.querySelector('form[action="actualizavoto.php"]').submit();
}
```

**Archivo modificado:** `carga_datos.php`

---

## 📊 Resumen de Cambios

| Archivo | Cambios | Líneas |
|---------|---------|--------|
| `docker-compose.yml` | Puerto 3310 → 3306 | 11, 25 |
| `ws/conexion.php` | Puerto 3310 → 3306 | 21, 29 |
| `ws/funciones.php` | ✅ Agregada `construirURL()` | 122-133 |
| `carga_datos.php` | ✅ Descomentar `$url`, agregar `$wsUrl`, agregar `buscarRegistro()` | 4-6, 33-43 |
| `cargar_datos1.php` | ✅ Agregar `$wsUrl` en JavaScript | 1-4 |
| `actualizavoto.php` | Validación, convertir a int, usar `construirURL()` | 5-33 |
| `index.php` | ✅ Agregar script `$wsUrl` | 58-62 |
| `inicio.php` | Ocultar Excel excepto Admin | 82-88 |
| `js/funciones.js` | Validación de `$wsUrl` | 37-72 |

---

## ✅ Estado Final

### Funcionando Correctamente:
- ✅ Conexión a BD (Docker + Producción)
- ✅ Registro de votos (Rol Veedor)
- ✅ Redireccionamiento automático (Local + Producción)
- ✅ Botón Excel solo para Admin
- ✅ Web Service calls sin errores
- ✅ Búsqueda por mesa + orden (Veedor)

### Pendiente:
- ⏳ Búsqueda por número de CI (Veedor)
- ⏳ Seguridad: 1 login simultáneo por usuario
- ⏳ Nuevas funcionalidades (informes PDF para Editor)

---

## 🔧 Roles y Niveles de Acceso

| Rol | `nivel_acceso` | Permisos |
|-----|-----------------|----------|
| Admin | 0 | Acceso total, puede descargar Excel |
| Editor | 1 | Gestión de datos, presupuestos |
| Veedor | 2 | Ver/registrar votos |
| Invitado | 3 | Acceso limitado |

---

## 📝 Función Clave Agregada

### `construirURL($archivo, $parametros = array())`
Ubicación: `ws/funciones.php`

Detecta automáticamente el entorno (Docker/Producción) y construye URLs correctas:
- ✅ Detecta protocolo (http/https)
- ✅ Incluye puerto automáticamente si es necesario
- ✅ Construye query string con `http_build_query()`
- ✅ Funciona en local (localhost:8080) y producción (votacion.com.py)

```php
// Uso:
header("Location: " . construirURL("carga_datos.php", array("status" => "ok")));
// Resultado en Docker: http://localhost:8080/carga_datos.php?status=ok
// Resultado en Producción: https://votacion.com.py/carga_datos.php?status=ok
```

---

## 🚀 Próximos Pasos

1. **Búsqueda por CI:** Implementar campo de búsqueda en Veedor
2. **Login Único:** Prevenir múltiples sesiones por usuario
3. **Informes PDF:** Agregar reportes para rol Editor
   - Gente que no votó por barrio
   - Gente que ya votó por barrio
   - Cantidad de votantes por mesa
   - Estadísticas por barrio

---

**Última actualización:** 8 de Mayo 2026



¡Encontré el problema! 🎯

  El usuario "veedor" en tu base de datos tiene nivel_acceso = 1 (que debería ser
  Editor), no 2 (que debería ser Veedor).

  Según el RESUMEN_ARREGLOS.md:
  - nivel_acceso = 0 → Admin
  - nivel_acceso = 1 → Editor
  - nivel_acceso = 2 → Veedor ← Este debería ser tu usuario
  - nivel_acceso = 3 → Invitado

  Necesitas cambiar en la base de datos:

  UPDATE usuarios SET nivel_acceso = 2 WHERE id = 74;

  O si prefieres usar el administrador:
  1. Accede como Admin
  2. Ve a Usuarios
  3. Edita el usuario "veedor" (ID 74)
  4. Cambia nivel_acceso de 1 a 2
  5. Guarda

  Después de eso, borra el archivo verificar.php porque ya no lo necesitaremos:

  rm verificar.php

  ¿Quieres que cambie la redirección a que veedor use nivel_acceso = 1, o prefieres
  corregir la base de datos?

✻ Brewed for 6s

※ recap: Estamos arreglando el enrutamiento del rol veedor en un sistema de votación.
   El usuario veedor en la base de datos tiene nivel_acceso=1 en lugar de 2. Próximo
  paso: cambiar nivel_acceso de 1 a 2 para el usuario veedor. (disable recaps in
  /config)