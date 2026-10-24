// proxy.cjs
const http = require('http');
const httpProxy = require('http-proxy');

// Change this host each time Serveo assigns a new one
const tunnelHost = 'siscom-unet.serveo.net';

const tunnelHeaders = {
  host: tunnelHost,
  'X-Forwarded-Proto': 'https',
  'X-Forwarded-Host': tunnelHost,
};

// Proxy HTTP → Laravel
const apiProxy = httpProxy.createProxyServer({
  target: 'http://127.0.0.1:8000',
  changeOrigin: true,
  headers: tunnelHeaders,
});

// Proxy WS → Reverb
const wsProxy = httpProxy.createProxyServer({
  target: 'ws://127.0.0.1:8080',
  ws: true,
  secure: false, // ⬅️ allow self-signed SSL
  changeOrigin: true,
  headers: tunnelHeaders,
});

const server = http.createServer((req, res) => {
  apiProxy.web(req, res);
});

// ESTO DEBE ESTAR PRESENTE: Escuchar el evento 'upgrade'
server.on('upgrade', (req, socket, head) => {
  console.log('--- WS UPGRADE START ---');
  console.log('Path WS:', req.url); 
  console.log('Target WS:', wsProxy.options.target);
  
  wsProxy.ws(req, socket, head);
});

// ESTO DEBE ESTAR PRESENTE: Manejo de Errores Específico para WS
wsProxy.on('error', (err, req, socket) => {
  console.error('❌ WS Proxy Error:', err.code, 'Mensaje:', err.message, 'URL:', req.url);
  if (socket) {
      socket.destroy(); 
  }
});

apiProxy.on('error', (err) => {
  console.error('❌ API Proxy Error:', err);
});



const PORT = process.env.PROXY_PORT || 3000;
server.listen(PORT, () =>
  console.log(`✅ Proxy corriendo en http://localhost:${PORT}`)
);
