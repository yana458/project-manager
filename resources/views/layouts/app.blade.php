<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Panel')</title>
</head>
<body style="margin:0; font-family: system-ui;">
  <header style="padding:12px 16px; border-bottom:1px solid #eee;">
    <strong>Gestión de Proyectos</strong>
  </header>

  <div style="display:flex; min-height: calc(100vh - 49px);">
    <aside style="width:220px; border-right:1px solid #eee; padding:12px;">
      <nav style="display:flex; flex-direction:column; gap:8px;">
        <a href="/dashboard">Dashboard</a>
        <a href="/clients">Clientes</a>
        <a href="/projects">Proyectos</a>
        <a href="/users">Usuarios</a>
      </nav>
    </aside>

    <main style="flex:1; padding:16px;">
      @yield('content')
    </main>
  </div>
</body>
</html>