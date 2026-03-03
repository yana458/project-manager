# 02 — Casos de uso

## Lista de casos de uso
- [UC-1] Iniciar sesión
- [UC-2] Gestionar usuarios (CRUD: crear/editar/listar/ver/desactivar)
- [UC-3] Asignar rol global a usuario (Admin/Senior/Junior)
- [UC-4] Gestionar clientes (CRUD: crear/editar/listar/ver/desactivar)
- [UC-5] Gestionar proyectos (CRUD: crear/editar/listar/ver)
- [UC-6] Gestionar equipo del proyecto (añadir usuario / cambiar rol interno / quitar)
- [UC-7] Cambiar estado del proyecto
- [UC-8] Archivar proyecto
- [UC-9] Cerrar sesión

---

## UC-1 — Iniciar sesión
**Descripción:** Permite a un usuario autenticarse y acceder al sistema con sus permisos (Admin/Senior/Junior).  
**Actores:** Usuario (Admin, Senior, Junior).  
**Precondiciones:** Usuario registrado y activo.  
**Postcondiciones:** Sesión iniciada y acceso al panel según rol/permisos.

### Secuencia normal
| # | Acción (actor) | Reacción (sistema) |
|---|---|---|
| 1 | Accede a la pantalla de login. | Muestra formulario (email/contraseña). |
| 2 | Introduce credenciales y envía. | Valida datos y autentica. |
| 3 | — | Redirige al panel según rol. |

### Excepciones
| Código | Acción (actor) | Reacción (sistema) |
|---|---|---|
| p | Credenciales incorrectas | Muestra error y permite reintentar. |
| q | Usuario inactivo/bloqueado | Muestra aviso y bloquea acceso. |

---

## UC-2 — Gestionar usuarios (CRUD)
**Descripción:** Permite al Administrador gestionar usuarios del sistema: listar/ver, crear, editar y desactivar.  
**Actores:** Admin.  
**Precondiciones:** Admin autenticado con permiso de gestión de usuarios.  
**Postcondiciones:** Usuarios creados/actualizados/desactivados.

### Secuencia normal
| # | Acción (actor) | Reacción (sistema) |
|---|---|---|
| 1 | Accede al módulo “Usuarios”. | Muestra listado con búsqueda/filtros. |
| 2 | Selecciona “Nuevo usuario”. | Muestra formulario de creación. |
| 3 | Introduce datos y guarda. | Valida datos (email único, formato…). |
| 4 | — | Crea el usuario y confirma. |
| 5 | Selecciona un usuario y pulsa “Editar”. | Muestra formulario precargado. |
| 6 | Guarda cambios. | Valida y actualiza usuario. |
| 7 | Pulsa “Desactivar” en un usuario. | Pide confirmación y marca inactivo. |

### Excepciones
| Código | Acción (actor) | Reacción (sistema) |
|---|---|---|
| p | Email duplicado / datos inválidos | Errores de validación. |
| q | Usuario no encontrado | 404 / no encontrado. |
| r | Usuario sin permisos | 403 / acceso denegado. |

---

## UC-3 — Asignar rol global a usuario
**Descripción:** Permite asignar o modificar el rol global de un usuario (Admin/Senior/Junior) usando Spatie.  
**Actores:** Admin.  
**Precondiciones:** Admin autenticado. Usuario existente. Roles configurados.  
**Postcondiciones:** Rol global asignado y permisos actualizados.

### Secuencia normal
| # | Acción (actor) | Reacción (sistema) |
|---|---|---|
| 1 | Abre la ficha/edición del usuario. | Muestra datos y rol actual. |
| 2 | Selecciona nuevo rol global. | Valida rol permitido. |
| 3 | Guarda. | Asigna rol (Spatie) y confirma. |

### Excepciones
| Código | Acción (actor) | Reacción (sistema) |
|---|---|---|
| p | Rol no válido | Error de validación. |
| q | Usuario no existe | 404 / no encontrado. |
| r | Sin permisos | 403 / acceso denegado. |

---

## UC-4 — Gestionar clientes (CRUD)
**Descripción:** Permite gestionar clientes: listar/ver, crear, editar y desactivar. Incluye datos fiscales y de contacto.  
**Actores:** Admin, Senior.  
**Precondiciones:** Autenticado con permisos de clientes.  
**Postcondiciones:** Clientes creados/actualizados/desactivados.

### Secuencia normal
| # | Acción (actor) | Reacción (sistema) |
|---|---|---|
| 1 | Accede a “Clientes”. | Muestra listado con búsqueda. |
| 2 | Crea cliente (nombre, CIF/DNI, dirección fiscal, teléfono, email, RRSS, WhatsApp, URL). | Valida y guarda. |
| 3 | Edita un cliente existente. | Valida y actualiza. |
| 4 | Desactiva un cliente. | Marca `is_active=false` y confirma. |
| 5 | Visualiza ficha de cliente. | Muestra datos y proyectos asociados (si aplica). |

### Excepciones
| Código | Acción (actor) | Reacción (sistema) |
|---|---|---|
| p | CIF/DNI duplicado o datos inválidos | Error de validación. |
| q | Cliente no encontrado | 404 / no encontrado. |
| r | Sin permisos | 403 / acceso denegado. |

---

## UC-5 — Gestionar proyectos (CRUD)
**Descripción:** Permite listar/ver, crear y editar proyectos. El acceso se limita por asignación (`project_user`) salvo Admin.  
**Actores:** Admin, Senior, Junior (según asignación).  
**Precondiciones:** Autenticado. Para ver: Admin o usuario asignado. Para crear/editar: permisos correspondientes.  
**Postcondiciones:** Proyectos creados/actualizados y visibles según reglas de acceso.

### Secuencia normal
| # | Acción (actor) | Reacción (sistema) |
|---|---|---|
| 1 | Accede a “Proyectos”. | Admin: todos. No admin: solo proyectos asignados (project_user). |
| 2 | Crea proyecto asociado a un cliente (nombre, fechas, URLs, prioridad). | Valida y crea. |
| 3 | — | Asigna al creador como `manager` (inserta en `project_user`). |
| 4 | Visualiza detalle del proyecto. | Comprueba acceso y muestra ficha. |
| 5 | Edita proyecto (campos permitidos). | Comprueba permisos/rol interno y actualiza. |

### Excepciones
| Código | Acción (actor) | Reacción (sistema) |
|---|---|---|
| p | No asignado (y no admin) intenta ver | 403 / acceso denegado. |
| q | Sin permiso de crear/editar | 403 / acceso denegado. |
| r | Proyecto o cliente no existe | 404 / validación. |
| s | Datos inválidos | Validación. |

---

## UC-6 — Gestionar equipo del proyecto
**Descripción:** Permite gestionar asignaciones del proyecto: añadir usuario, cambiar rol interno (manager/editor/viewer) y quitar usuario.  
**Actores:** Admin, Senior (si es manager del proyecto).  
**Precondiciones:** Autenticado, acceso al proyecto, permiso `projects.assign_users` y rol interno `manager`.  
**Postcondiciones:** `project_user` actualizado.

### Secuencia normal
| # | Acción (actor) | Reacción (sistema) |
|---|---|---|
| 1 | Entra al proyecto y abre “Equipo”. | Muestra miembros. |
| 2 | Añade usuario y rol interno. | Inserta en `project_user`. |
| 3 | Cambia rol interno de un miembro. | Actualiza `project_user`. |
| 4 | Quita usuario del proyecto. | Elimina fila de `project_user`. |

### Excepciones
| Código | Acción (actor) | Reacción (sistema) |
|---|---|---|
| p | Usuario no existe o rol inválido | Validación. |
| q | Sin permisos y no admin | 403 / acceso denegado. |
| r | Quitar al último manager | Deniega y avisa. |

---

## UC-7 — Cambiar estado del proyecto
**Descripción:** Permite actualizar el estado del proyecto (new, in_progress, blocked, delivered, etc.).  
**Actores:** Admin, Senior.  
**Precondiciones:** Acceso al proyecto + permiso `projects.change_status` (y rol interno manager si lo aplicáis).  
**Postcondiciones:** Estado actualizado y visible.

### Secuencia normal
| # | Acción (actor) | Reacción (sistema) |
|---|---|---|
| 1 | Entra al proyecto. | Verifica acceso. |
| 2 | Selecciona nuevo estado. | Valida estado permitido. |
| 3 | Guarda cambios. | Actualiza `status` y confirma. |

### Excepciones
| Código | Acción (actor) | Reacción (sistema) |
|---|---|---|
| p | Sin permiso/rol interno insuficiente | 403 / acceso denegado. |
| q | Estado inválido | Validación. |

---

## UC-8 — Archivar proyecto
**Descripción:** Archiva un proyecto (por ejemplo `status = archived`) para retirarlo del flujo activo sin borrarlo.  
**Actores:** Admin, Senior.  
**Precondiciones:** Acceso al proyecto + permiso para archivar.  
**Postcondiciones:** Proyecto archivado.

### Secuencia normal
| # | Acción (actor) | Reacción (sistema) |
|---|---|---|
| 1 | Entra al proyecto. | Comprueba acceso. |
| 2 | Pulsa “Archivar”. | Pide confirmación. |
| 3 | Confirma. | Cambia estado a `archived` y confirma. |

### Excepciones
| Código | Acción (actor) | Reacción (sistema) |
|---|---|---|
| p | Sin permiso/rol suficiente | 403 / acceso denegado. |
| q | Cancela confirmación | Sin cambios. |

---

## UC-9 — Cerrar sesión
**Descripción:** El usuario cierra la sesión activa de forma segura.  
**Actores:** Admin, Senior, Junior.  
**Precondiciones:** Sesión iniciada.  
**Postcondiciones:** Sesión finalizada y vuelta al login/inicio.

### Secuencia normal
| # | Acción (actor) | Reacción (sistema) |
|---|---|---|
| 1 | Pulsa “Cerrar sesión”. | Cierra sesión y limpia sesión/token. |
| 2 | — | Redirige al login/inicio. |