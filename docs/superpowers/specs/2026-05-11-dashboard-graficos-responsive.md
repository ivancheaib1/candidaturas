# Dashboard Responsive de Gráficos - Sistema de Votación
**Fecha:** 2026-05-11  
**Objetivo:** Mejorar y expandir los gráficos existentes con diseño responsive y múltiples visualizaciones de datos

---

## 1. Resumen Ejecutivo

Crear un dashboard responsive que visualice datos de votación desde la base de datos `hcmisiones`. El dashboard incluirá 6 visualizaciones (2 KPI cards + 4 gráficos) que se adapten automáticamente a cualquier dispositivo. Migrar de Chart.js 2.8 a 4.x para mejor rendimiento y soporte responsive nativo.

---

## 2. Datos Disponibles

Tabla principal: `hc_padron` (7941 registros)

### Dimensiones de análisis:
- **Geográficas:** Distrito (desc_dis), Sección (desc_sec), Barrio (barrio)
- **De voto:** voto_registrado (0/1), voto1-5, intension_voto, presupuesto
- **Demográficas:** fecha_naci, partido
- **Operacionales:** mesa, operador, usuario_mod

---

## 3. Especificación de Visualizaciones

### 3.1 KPI Cards (Row 1)

#### Card 1: Total de Empadronados
```
Métrica: COUNT(*) FROM hc_padron
Formato: Número grande (18px+) + Etiqueta
Descripción: "Total de votantes empadronados"
Color: #007BFF (Azul Bootstrap)
```

#### Card 2: Participación (%)
```
Métrica: (SUM(voto_registrado=1) / COUNT(*)) * 100
Formato: Número grande + Símbolo % + Etiqueta
Descripción: "Tasa de participación actual"
Color: #28A745 (Verde Bootstrap)
Rótulo adicional: Actualizado en tiempo real
```

---

### 3.2 Gráficos Principales

#### Gráfico 1: Votos por Distrito (Barras Horizontal)
```
Tipo: Bar (indexAxis: 'y')
Eje X: Cantidad de votantes
Eje Y: Distritos (desc_dis)
Series:
  - "Si votaron" (voto_registrado=1) → Color verde
  - "No votaron" (voto_registrado=0) → Color rojo
Datos: GROUP BY desc_dis
Layout: Responsive (50% width en desktop, 100% en mobile)
Leyenda: Debajo del gráfico
```

#### Gráfico 2: Distribución por Sección (Pie)
```
Tipo: Pie/Donut
Datos: COUNT(*) GROUP BY desc_sec
Segmentos: 10 secciones con colores diferenciados
Interactividad: Mostrar porcentaje en hover
Leyenda: Lateral (derecha en desktop, abajo en mobile)
Layout: Responsive (50% width en desktop, 100% en mobile)
```

#### Gráfico 3: Top 10 Barrios (Barras Vertical)
```
Tipo: Bar (vertical)
Eje X: Barrios (top 10 por cantidad)
Eje Y: Cantidad de votantes
Series: Single series con gradiente de color (verde alto → rojo bajo)
Datos: COUNT(*) GROUP BY barrio LIMIT 10
Layout: Responsive (50% width en desktop, 100% en mobile)
```

#### Gráfico 4: Intención de Voto por Distrito (Área Apilada)
```
Tipo: Line con fill (área apilada)
Eje X: Distritos (desc_dis)
Eje Y: Cantidad de votantes
Series:
  - voto1 (color 1)
  - voto2 (color 2)
  - voto3 (color 3)
  - voto4 (color 4)
  - voto5 (color 5)
Datos: SUM para cada voto por distrito
Layout: Responsive (50% width en desktop, 100% en mobile)
Leyenda: Debajo
```

---

## 4. Diseño Responsive

### Grid Layout:
- **Desktop (≥992px):** 2 columnas, 2 filas
  - Fila 1: 2 KPI cards (50% ancho cada uno)
  - Fila 2: Gráfico 1 + Gráfico 2 (50% ancho cada uno)
  - Fila 3: Gráfico 3 + Gráfico 4 (50% ancho cada uno)

- **Tablet (768px-992px):** 1 columna
  - Todos los elementos al 100% de ancho

- **Mobile (<768px):** 1 columna
  - Todos los elementos al 100% de ancho
  - Fuentes reducidas para KPI cards

### Espaciado:
- Gap entre elementos: 1rem
- Padding interno: 1rem
- Padding en contenedor: 1rem (mobile) a 2rem (desktop)

---

## 5. Stack Técnico

- **Frontend:** HTML5 + CSS3 + Bootstrap 5
- **Gráficos:** Chart.js 4.x (CDN)
- **Backend:** PHP 7.4+
- **Base de datos:** MySQL (hcmisiones)
- **Compatibilidad:** Chrome, Firefox, Safari, Edge (últimas 2 versiones)

---

## 6. Archivo Principal

**Ruta:** `/graficos.php`

### Estructura:
1. **Header/Navbar** - Existente (encabezado.php)
2. **Container responsive** - Bootstrap grid
3. **KPI Cards** - HTML con CSS personalizado
4. **Gráficos** - Canvas elements + Chart.js scripts
5. **Footer** - Existente (pie.php)

### Datos:
- Queries SQL optimizadas y separadas por métrica
- Cached en variables PHP (sin peticiones AJAX por ahora)
- Formato JSON dentro de scripts inline

---

## 7. Colores y Estilos

### Paleta:
- Votos positivos: `#28A745` (verde)
- Votos negativos: `#DC3545` (rojo)
- KPI primario: `#007BFF` (azul)
- KPI secundario: `#28A745` (verde)
- Fondos: `#F8F9FA` (gris claro)
- Texto: `#212529` (gris oscuro)

### Tipografía:
- Fuente: Lato (existente en Chart.js defaults)
- KPI: 28-36px bold
- Labels: 14-16px
- Títulos: 18-20px bold

---

## 8. Criterios de Aceptación

- ✅ Todos los gráficos se renderizan correctamente en desktop, tablet, mobile
- ✅ KPI cards muestran datos precisos y actualizados
- ✅ Gráficos mantienen legibilidad en todos los tamaños
- ✅ Leyendas se reposicionan automáticamente (lateral/abajo)
- ✅ Sin scroll horizontal en dispositivos móviles
- ✅ Carga rápida (datos desde BD, no AJAX)
- ✅ Sin errores en consola JavaScript

---

## 9. Plan de Implementación

Ver documento de plan separado: `writing-plans` (próximo paso)

---

## 10. Notas Técnicas

- La migración de Chart.js 2.8 → 4.x requiere ajustes en sintaxis (`Chart.defaults.global` → `Chart.defaults`)
- Responsive nativo de Chart.js 4.x reduce necesidad de CSS personalizado
- Queries SQL están optimizadas para 7941 registros (performance acceptable)
- Bootstrap 5 incluido en proyecto (usar clases existentes)
