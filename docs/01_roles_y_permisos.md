# 01 — Roles y permisos

## Objetivo del control de accesos
El sistema debe garantizar que:
1. Cada usuario tiene capacidades según su **rol global** (Admin / Senior / Junior).
2. Un usuario **solo puede acceder a proyectos donde esté asignado**, salvo el Administrador.
3. Dentro de un proyecto se puede limitar **quién edita** y quién tiene **solo lectura**.

## Enfoque elegido
- **RBAC** (Role-Based Access Control) con **Spatie Laravel Permission** para roles/permisos globales.
- **Asignación por proyecto** mediante tabla pivote `project_user` (miembro + rol interno).
- **Policies** como validación final (por ejemplo: `ProjectPolicy`, `ClientPolicy`).

> Nota: el rol global (Spatie) y el rol interno del proyecto (`project_user.project_role`) son cosas distintas y pueden combinarse.

---

## Roles globales (Spatie)
### admin
Administración total:
- Usuarios, roles, permisos
- Clientes, proyectos
- Acceso completo a todos los proyectos

### senior
Perfil tipo Project Manager:
- Ver/crear/editar clientes y proyectos (según permisos)
- Gestionar equipo del proyecto si además es `manager` en ese proyecto

### junior
Perfil operativo:
- Acceso solo a proyectos asignados
- Puede editar información operativa si su rol interno lo permite

---

## Rol dentro del proyecto (project_user.project_role)
- **manager**: gestiona equipo del proyecto (altas/bajas/cambios de rol interno) y cambios relevantes.
- **editor**: actualiza información operativa (seguimiento y algunos campos).
- **viewer**: solo lectura.

---

## Permisos  
Puedes definir permisos de Spatie de forma granular. Ejemplo:

### Usuarios / seguridad
- `users.manage`
- `roles.manage`
- `permissions.manage`

### Clientes
- `clients.view`
- `clients.create`
- `clients.edit`
- `clients.deactivate`

### Proyectos
- `projects.view`
- `projects.create`
- `projects.edit`
- `projects.change_status`
- `projects.archive`
- `projects.assign_users`

---

## Matriz orientativa: rol global vs acciones
> Luego las **Policies** y el rol interno del proyecto terminan de decidir el acceso real.

| Acción |               Admin | Senior | Junior |
|-----------------------|:------:|:------:|:----:|
| Iniciar/Cerrar sesión | ✅ | ✅ | ✅ |
| Gestionar usuarios (CRUD + desactivar) | ✅ | ❌ | ❌ |
| Asignar rol global a usuario | ✅ | ❌ | ❌ |
| Gestionar clientes (CRUD + desactivar) | ✅ | ✅ | ❌ |
| Gestionar proyectos (CRUD) | ✅ | ✅ | ⚠️ Solo asignados |
| Gestionar equipo del proyecto | ✅ | ✅ (si es manager) | ❌ |
| Cambiar estado / Archivar proyecto | ✅ | ✅ | ❌ |

---

## Reglas clave (resumen)
1. Si es **admin**, acceso total.
2. Si no es admin:
   - Solo ve proyectos donde exista fila en `project_user`.
   - Dentro del proyecto, se aplica el rol interno (`manager/editor/viewer`).
3. Las Policies (`ProjectPolicy`, `ClientPolicy`) son la última capa de validación.