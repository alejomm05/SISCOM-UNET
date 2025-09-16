<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - SISCOM UNET</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 40px;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            color: #2c3e50;
        }

        .section {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 30px;
        }

        .section h2 {
            margin-top: 0;
            color: #34495e;
        }

        input {
            padding: 10px;
            margin: 5px;
            width: calc(100% - 22px);
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        pre {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 14px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>

    <h1>🔧 Panel de Administración SISCOM UNET</h1>

    <!-- Login -->
    <div class="section">
        <h2>🔐 Iniciar Sesión</h2>
        <div class="input-group">
            <input type="email" id="email" placeholder="Email" value="admin@ejemplo.com" />
            <input type="password" id="password" placeholder="Contraseña" value="password" />
        </div>
        <button onclick="login()">Login</button>
        <pre id="login-response">Respuesta del login aparecerá aquí...</pre>
    </div>

    <!-- Listar usuarios -->
    <div class="section">
        <h2>📋 Listar Guardias</h2>
        <button onclick="listUsers()">Listar Guardias</button>
        <pre id="users-response">Lista de usuarios aparecerá aquí...</pre>
    </div>

    <!-- Crear guardia -->
    <div class="section">
        <h2>➕ Crear Guardia</h2>
        <div class="input-group">
            <input type="text" id="new-name" placeholder="Nombre" value="Guardia Nuevo" />
            <input type="email" id="new-email" placeholder="Email" value="nuevo@guardia.com" />
            <input type="text" id="new-code" placeholder="Código secreto" value="INVITE-GUARD-2025" />
            <input type="password" id="new-password" placeholder="Contraseña" value="12345678" />
        </div>
        <button onclick="createUser()">Crear Guardia</button>
        <pre id="create-response">Respuesta de creación aparecerá aquí...</pre>
    </div>

    <!-- Eliminar guardia -->
    <div class="section">
        <h2>🗑️ Eliminar Guardia</h2>
        <div class="input-group">
            <input type="number" id="delete-id" placeholder="ID del guardia a eliminar" />
        </div>
        <button onclick="deleteUser()">Eliminar Guardia</button>
        <pre id="delete-response">Respuesta de eliminación aparecerá aquí...</pre>
    </div>

    <script>
        let token = null;

        async function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await axios.post('/api/login', { email, password });
                token = response.data.token;
                document.getElementById('login-response').textContent = JSON.stringify(response.data, null, 2);
                alert('¡Login exitoso!');
            } catch (error) {
                document.getElementById('login-response').textContent = JSON.stringify(error.response?.data || error.message, null, 2);
            }
        }

        async function listUsers() {
            if (!token) return alert('Primero debes hacer login.');

            try {
                const response = await axios.get('/api/admin/users', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                document.getElementById('users-response').textContent = JSON.stringify(response.data, null, 2);
            } catch (error) {
                document.getElementById('users-response').textContent = JSON.stringify(error.response?.data || error.message, null, 2);
            }
        }

        async function createUser() {
            if (!token) return alert('Primero debes hacer login.');

            const name = document.getElementById('new-name').value;
            const email = document.getElementById('new-email').value;
            const code = document.getElementById('new-code').value;
            const password = document.getElementById('new-password').value;

            try {
                const response = await axios.post('/api/admin/users', {
                    name, email, code, password
                }, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                document.getElementById('create-response').textContent = JSON.stringify(response.data, null, 2);
                alert('¡Guardia creado!');
            } catch (error) {
                document.getElementById('create-response').textContent = JSON.stringify(error.response?.data || error.message, null, 2);
            }
        }

        async function deleteUser() {
            if (!token) return alert('Primero debes hacer login.');

            const id = document.getElementById('delete-id').value;

            try {
                const response = await axios.delete(`/api/admin/users/${id}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                document.getElementById('delete-response').textContent = JSON.stringify(response.data, null, 2);
                alert('¡Guardia eliminado!');
            } catch (error) {
                document.getElementById('delete-response').textContent = JSON.stringify(error.response?.data || error.message, null, 2);
            }
        }
    </script>

</body>
</html>
