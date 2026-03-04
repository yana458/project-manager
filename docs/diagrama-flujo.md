# Diagrama de Flujo — Sistema de Gestión de Proyectos

---

## INICIO

```
[INICIO]
   │
   ▼
[Iniciar Sesión]
   │
   ▼
◆ ¿Credenciales válidas?
   ├── NO ──► [Mostrar error de autenticación] ──► [FIN (reintentar)]
   │
   └── SÍ
        │
        ▼
      ◆ ¿Rol del Usuario?
         ├── Admin    → Acceso total a todos los proyectos
         ├── Senior/PM → Acceso solo a proyectos asignados
         └── Junior   → Acceso solo a lectura de proyectos asignados
              │
              ▼
        [Dashboard Principal]
```

---

## Dashboard Principal

Desde el Dashboard, el usuario puede:

- **Cerrar Sesión** (UC-9)
- Navegar a los módulos: **Usuarios**, **Proyectos**, **Clientes**

---

## UC-9 — Cerrar Sesión

> Disponible para todos los roles.

```
[Cerrar Sesión]
   │
   ▼
[Destruir sesión → Limpiar tokens]
   │
   ▼
[Redirigir a pantalla de Login → FIN]
```

---

## UC-2 — Módulo de Usuarios

> Solo Administradores.

```
◆ ¿Acción a realizar?
   ├── Listar        → Ver tabla de usuarios
   ├── Ver Detalle   → Consultar ficha del usuario
   ├── Crear         → Formulario → Validar → Guardar
   ├── Editar        → Modificar datos → Validar → Guardar
   ├── Desactivar    → Confirmar → Cambiar estado a inactivo
   └── Asignar Roles → Actualizar permisos en el sistema
         │
         ▼
       ◆ ¿Tienes permiso para esta acción?
          ├── NO ──► [Acción bloqueada]
          └── SÍ ──► Ejecutar acción correspondiente
```

---

## UC-3 — Gestionar Usuarios (CRUD)

> Solo Administradores.

| Acción | Flujo |
|---|---|
| **Listar** | Ver tabla de usuarios |
| **Ver detalle** | Consultar ficha del usuario |
| **Crear** | Formulario → Validar → Guardar |
| **Editar** | Modificar datos → Validar → Guardar |
| **Desactivar** | Confirmar → Cambiar estado a inactivo |
| **Asignar roles** | Actualizar permisos en el sistema |

---

## UC-5 — Módulo de Proyectos

> **Admin**: acceso completo a todos los proyectos.  
> **Senior**: puede editar y crear sus proyectos asignados.  
> **Junior**: solo puede listar y ver detalles de sus proyectos asignados.

```
◆ ¿Acción a realizar?
   │
   ├── [Gestionar Proyectos]
   │       │
   │       ▼
   │     ◆ ¿Acción a realizar?
   │        ├── Listar       (todos los roles, dentro de su acceso)
   │        ├── Ver Detalles (todos los roles, dentro de su acceso)
   │        ├── Crear        → Admin / Senior
   │        ├── Editar       → Admin / Senior (del proyecto)
   │        └── Archivar
   │               │
   │               ▼
   │             ◆ ¿Tienes permiso para esta acción?
   │                ├── NO ──► [Acción bloqueada]
   │                └── SÍ
   │                     ├── Listar/Ver → Todos los roles (dentro de su acceso)
   │                     ├── Crear      → Admin / Senior
   │                     ├── Editar     → Admin / Senior (del proyecto)
   │                     └── Archivar   → Confirmar → Proyecto pasa a solo lectura
   │
   ├── [Gestionar Estados de Proyectos] ──► UC-7
   │
   └── [Gestionar Equipos] ──► UC-6
```

---

## UC-7 — Gestionar Estados del Proyecto

> Admin + Senior del proyecto.

```
◆ ¿Acción a realizar?
   └── Editar estado
          │
          ▼
        Estados disponibles:
        • Pendiente
        • En curso
        • Finalizado
          │
          ▼
        ◆ ¿Transición de estado válida?
           ├── NO ──► [Acción bloqueada]
           └── SÍ
                │
                ▼
              ◆ Estado == "Finalizado"?
                 ├── NO ──► [Acción bloqueada]
                 └── SÍ ──► Confirmar → Actualizar estado → Registrar cambio
```

---

## UC-8 — Archivar Proyecto

> Solo Administradores.

```
[Archivar proyecto]
   │
   ▼
◆ ¿Tienes permiso?
   ├── NO ──► [Acción bloqueada]
   └── SÍ
        │
        ▼
      ◆ Estado == "Finalizado"?
         ├── NO ──► [Acción bloqueada]
         └── SÍ ──► Confirmar → Proyecto pasa a solo lectura
```

---

## UC-6 — Gestionar Equipos del Proyecto

> Admin + Senior del proyecto.

```
◆ ¿Acción a realizar?
   ├── Cambiar rol interno
   ├── Añadir Usuario
   └── Quitar Usuario
         │
         ▼
       ◆ ¿Tienes permiso para esta acción?
          ├── NO ──► [Acción bloqueada]
          └── SÍ
               ├── Añadir usuario   → Buscar usuario → Asignar rol interno → Guardar
               ├── Cambiar rol      → Seleccionar miembro → Nuevo rol → Guardar
               └── Quitar usuario   → Seleccionar miembro → Confirmar → Eliminar del equipo
```

---

## UC-4 — Módulo de Clientes

> **Admin**: acceso completo (CRUD).  
> **Senior / Junior**: solo listar y ver detalles.

```
◆ ¿Acción a realizar?
   ├── Listar       (disponible para todos los roles)
   ├── Ver Detalles (disponible para todos los roles)
   ├── Crear        → Solo Admin (y Senior según configuración)
   ├── Editar       → Solo Admin (y Senior según configuración)
   └── Desactivar   → Solo Admin → Confirmar → Cambiar estado
         │
         ▼
       ◆ ¿Tienes permiso para esta acción?
          ├── NO ──► [Acción bloqueada]
          └── SÍ
               ├── Listar/Ver   → Disponible para todos los roles
               ├── Crear/Editar → Solo Admin (y Senior según configuración)
               └── Desactivar   → Solo Admin → Confirmar → Cambiar estado
```

---

## Resumen de Permisos por Rol

| Módulo / Acción | Admin | Senior | Junior |
|---|:---:|:---:|:---:|
| Iniciar / Cerrar sesión | ✅ | ✅ | ✅ |
| Gestionar usuarios (CRUD) | ✅ | — | — |
| Asignar rol global | ✅ | — | — |
| Clientes — Ver / Listar | ✅ | ✅ | ✅ |
| Clientes — Crear / Editar / Desactivar | ✅ | — | — |
| Proyectos — Ver / Listar | ✅ Todos | ⚠️ Asignados | ⚠️ Asignados |
| Proyectos — Crear / Editar | ✅ | ⚠️ Asignados | — |
| Gestionar equipo del proyecto | ✅ | ⚠️ Su proyecto | — |
| Cambiar estado del proyecto | ✅ | ⚠️ Su proyecto | — |
| Archivar proyecto | ✅ | — | — |

> ✅ Acceso completo · ⚠️ Acceso parcial/condicional · — Sin acceso
