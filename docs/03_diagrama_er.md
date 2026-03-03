# 03 — Diagrama Entidad–Relación (E-R)

## Resumen de entidades y relaciones
- **User** (tabla `users` de Laravel)
  - Roles/permisos globales con **Spatie**
- **Client**
  - 1:N con Project (un cliente tiene varios proyectos)
- **Project**
  - Pertenece a un Client
  - N:N con User mediante pivote `project_user` (equipo asignado + rol interno)
  - (Opcional/futuro) 1:N con `project_updates` (seguimiento)
  - (Opcional/futuro) 1:N con `project_files` (archivos/entregables)

---

## Tablas principales

### clients
Campos recomendados:
- `id` (PK)
- `name` (obligatorio)
- `tax_id` (CIF/DNI)
- `fiscal_address` (texto)
- `phone`, `email`
- `whatsapp`
- `website_url`
- `social_links` (JSON)
- `notes` (opcional)
- `is_active` (boolean, default true)
- `created_at`, `updated_at` (+ opcional `deleted_at`)

### projects
Campos recomendados:
- `id` (PK)
- `client_id` (FK → clients.id)
- `name` (obligatorio)
- `description` (nullable)
- `status` (new, in_progress, in_review, blocked, delivered, archived)
- `priority` (1 alta / 2 media / 3 baja)
- `start_date` (nullable)
- `due_date` (nullable)
- URLs opcionales: `repo_url`, `staging_url`, `production_url`, `docs_url`
- `created_by` (FK → users.id, nullable)
- `created_at`, `updated_at` (+ opcional `deleted_at`)

### project_user
Objetivo: controlar acceso real: quién pertenece al proyecto y con qué nivel.
- `project_id` (FK → projects.id)
- `user_id` (FK → users.id)
- `project_role` (manager | editor | viewer)
- `created_at`, `updated_at`
Restricciones:
- `unique(project_id, user_id)` para evitar duplicados

---

## Diagrama E-R (Mermaid)

```mermaid
erDiagram
    CLIENTS ||--o{ PROJECTS : owns
    PROJECTS ||--o{ PROJECT_USER : includes
    USERS ||--o{ PROJECT_USER : participates

    USERS {
      bigint id PK
      string name
      string email
      timestamps timestamps
    }

    CLIENTS {
      bigint id PK
      string name
      string tax_id
      text fiscal_address
      string phone
      string email
      string whatsapp
      string website_url
      json social_links
      boolean is_active
      timestamps timestamps
    }

    PROJECTS {
      bigint id PK
      bigint client_id FK
      string name
      text description
      string status
      tinyint priority
      date start_date
      date due_date
      string repo_url
      string staging_url
      string production_url
      string docs_url
      bigint created_by FK
      timestamps timestamps
    }

    PROJECT_USER {
      bigint project_id FK
      bigint user_id FK
      string project_role
      timestamps timestamps
    }